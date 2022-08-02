<?php

namespace App\Traits\Paperwork;

use App\Models\Broker;
use App\Models\Carrier;
use App\Models\Driver;
use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

trait PaperworkFilesFunctions
{
    /**
     * @param string $type
     * @return array
     */
    private function getPaperworkByType(string $type, $id = null): array
    {
        $broker_id = auth()->user()->broker_id;
        return [
            'filesUploads' => Paperwork::whereNull('template')
                ->where('broker_id', $broker_id)
                ->where('type', $type)
                ->where(function ($q) use ($type, $id) {
                    $q->whereHas('shipper', function ($q) use ($type, $id) {
                        switch ($type) {
                            case 'driver':
                                $driver = Driver::with([
                                    'truck' => function ($q) {
                                        $q->with([
                                            'trailer' => function ($q) {
                                                $q->with(['shippers' => function ($q) {
                                                    $q->select('id');
                                                }])
                                                    ->select(['id']);
                                            }
                                        ])
                                            ->select(['id','driver_id','trailer_id']);
                                    },
                                ])
                                    ->find($id);
                                $shippers = $driver->shippers_ids ?? [];
                                $q->whereIn('id', $shippers);
                                break;
                            default:
                                break;
                        }
                    })
                        ->orWhereDoesntHave('shipper');
                })
                ->orderBy('required', 'DESC')
                ->get(),
            'filesTemplates' => Paperwork::whereNotNull('template')
                ->where('broker_id', $broker_id)
                ->where('type', $type)
                ->orderBy('required', 'DESC')
                ->get(['id', 'name', 'required']),
        ];
    }

    private function getFilesPaperwork(object $paperworkArray, int $related_id)
    {
        $ids = [];
        foreach ($paperworkArray as $item)
            $ids[] = $item->id;
        return PaperworkFile::whereIn('paperwork_id', $ids)
            ->where('related_id', $related_id)
            ->get()
            ->keyBy('paperwork_id')
            ->toArray();
    }

    private function getTemplatesPaperwork(object $paperworkArray, int $related_id)
    {
        $ids = [];
        foreach ($paperworkArray as $item)
            $ids[] = $item->id;
        return PaperworkTemplate::whereIn('paperwork_id', $ids)
            ->where('related_id', $related_id)
            ->get(['id', 'paperwork_id'])
            ->keyBy('paperwork_id')
            ->toArray();
    }

    private function getFormattedJsonType($json)
    {
        $type = null;
        if (isset($json->text))
            $type = "text";
        if (isset($json->answers))
            $type = "radio";
        if (isset($json->signature))
            $type = "signature";
        if (isset($json->carrier))
            $type = "carrier";
        if (isset($json->driver))
            $type = "driver";
        if (isset($json->company))
            $type = "company";
        if (isset($json->date))
            $type = "date";
        if (isset($json->image))
            $type = "image";
        return $type;
    }

    private function getGeneralType($type, $json)
    {
        switch ($type) {
            case 'carrier':
                switch ($json->carrier) {
                    case 'name':
                    case 'address':
                    case 'phone':
                        $type = 'text';
                        break;
                }
                break;
            case 'driver':
                switch ($json->driver) {
                    case 'name':
                    case 'address':
                    case 'phone':
                        $type = 'text';
                        break;
                }
                break;
            case 'company':
                switch ($json->company) {
                    case 'name':
                    case 'address':
                    case 'phone':
                        $type = 'text';
                        break;
                    /*case 'signature':
                        $type = 'signature';
                        break;*/
                    // Instead of saving file, it will be queried on the pdf
                }
                break;
        }
        return $type;
    }

    /**
     * @param string $template
     * @param null $related_id
     * @param false $simpleVars
     * @return array
     */
    private function renderHtmlVars(string $template, $related_id = null, bool $simpleVars = false, $driver = null): array
    {
        preg_match_all("/{{[^}]*}}/", $template, $result);

        $matches = ["/{{\"date\"}}/", "/{{/", "/}}/", "/,\s/", "/\"validate\"/","/\"optional\"/","/\"signature\"/"];
        $replacements = ["{{\"date\":true}}", "{", "}", ",", "\"validate\":true","\"optional\":true","\"signature\":true"];

        if (!$simpleVars) {
            $carrier = null;
            $company = $driver ? $driver->broker : Broker::find(session('broker'));
            if (auth()->guard('carrier')->check()) {
                $carrier = auth()->user();
            } else if (auth()->guard('driver')->check()) {
                $carrier = auth()->user()->load('carrier')->carrier;
            } else if (auth()->guard('web')->check()) {
                $carrier = Carrier::find($related_id);
            } else if ($driver) {
                $carrier = $driver->carrier;
            }
            if (auth()->guard('carrier')->check()) {
                $driver = Driver::where('carrier_id', auth()->user()->id)->find($related_id);
            } else if (auth()->guard('driver')->check()) {
                $driver = auth()->user();
            } else if (auth()->guard('web')->check()) {
                $driver = Driver::find($related_id);
            }
            $date = Carbon::now()->format('m-d-Y');
        } else {
            return compact('result', 'matches', 'replacements');
        }

        return compact( 'result','matches', 'replacements', 'carrier', 'driver', 'company','date');
    }

    public function templateToHtml(Paperwork $paperwork, $related_id = null, $driver = null)
    {
        $replaced = [];
        $canvases = [];
        $validation = [];

        $vars = $this->renderHtmlVars($paperwork->template, $related_id, false, $driver);
        $matches = $vars["matches"];
        $replacements = $vars["replacements"];
        $carrier = $vars["carrier"];
        $driver = $vars["driver"];
        $company = $vars["company"];
        $result = $vars["result"];
        $date = $vars["date"];

        $images = $paperwork->images->toArray();

        $signature = null;
        switch ($paperwork->type) {
            case 'driver':
                $signature = $driver->signature;
                break;
            case 'carrier':
                $signature = $carrier->signature;
                break;
        }
        $signatureCount = $signature ? 1 : 0;
        foreach ($result[0] as $idx => $element) {
            $formatted = preg_replace($matches, $replacements, $element);
            $json = json_decode($formatted);
            $type = $this->getFormattedJsonType($json);
            $inputName = 'name="input-' . $idx . '"';
            if (isset($json->optional) && !isset($json->validate)) {
                $required = "";
            } else {
                $required = "required";
            }
            switch ($type) {
                case "text":
                    $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . $json->text . '" ' . $required . '></div>';
                    break;
                case 'radio':
                    $html = "<h4 class='m-0'>$json->text</h4>\r\n";
                    if (isset($json->validate))
                        shuffle($json->answers);
                    foreach ($json->answers as $i => $answer) {
                        $radioId = 'input-' . $idx . "-" . $i;
                        $html .= '<input type="' . $type . '" ' . $inputName . ' id="' . $radioId . '" value="' . $answer . '" ' . $required . '><label class="col-form-label" for="' . $radioId . '">' . $answer . "</label>\r\n";
                    }
                    $replaced[] = $html;
                    if (isset($json->validate))
                        $validation[] = "input-$idx";
                    break;
                case 'signature':
                    if ($signatureCount === 0) {
                        $canvasId = 'signature-' . $idx;
                        $replaced[] = '<div class="form-group text-center">' .
                            '<label class="col-form-label" for="' . $canvasId . '">Signature</label>' .
                            '<div>' .
                            '<canvas class="d-block mx-auto" id="' . $canvasId . '"></canvas>' .
                            '<button type="button" class="btn btn-outline-danger mt-1">Clear</button>' .
                            '</div>' .
                            '</div>';
                        $canvases[] = $canvasId;
                    } else {
                        $replaced[] = '<div class="form-group text-center">'.
                            '<fieldset>'.
                            '<label class="col-form-label d-block" for="' . $inputName . '">Signature</label>' .
                            '<div class="vs-checkbox-con vs-checkbox-primary justify-content-center">'.
                            '<input type="checkbox" value="signed" id="' . $inputName .'" ' . $required . '>'.
                            '<span class="vs-checkbox">'.
                            '<span class="vs-checkbox--check">'.
                            '<i class="vs-icon feather icon-check"></i>'.
                            '</span>'.
                            '</span>'.
                            '<span class="">Sign with previous signature</span>'.
                            '</div>'.
                            '</fieldset>'.
                            '</div>';
                    }
                    $signatureCount++;
                    break;
                case 'date':
                    $replaced[] = '<div class="form-group d-inline-block m-0">' . $date .'</div>';
                    break;
                case 'carrier':
                    switch ($json->carrier) {
                        case 'name':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . session('renames') ? session('renames')->carrier ?? 'Carrier' : 'Carrier'.' name' . '" ' . $required . ' value="' . ($carrier->name ?? null) . '"></div>';
                            break;
                        case 'owner':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Owner name' . '" ' . $required . ' value="' . ($carrier->owner ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . session('renames') ? session('renames')->carrier ?? 'Carrier' : 'Carrier'.' address' . '" ' . $required . ' value="' . ($carrier->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . session('renames') ? session('renames')->carrier ?? 'Carrier' : 'Carrier'.' phone' . '" ' . $required . ' value="' . ($carrier->phone ?? null) . '"></div>';
                            break;
                    }
                    break;
                case 'driver':
                    switch ($json->driver) {
                        case 'name':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver name' . '" ' . $required . ' value="' . ($driver->name ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver address' . '" ' . $required . ' value="' . ($driver->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver phone' . '" ' . $required . ' value="' . ($driver->phone ?? null) . '"></div>';
                            break;
                    }
                    break;
                case 'company':
                    switch ($json->company) {
                        case 'name':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company name' . '" ' . $required . ' value="' . ($company->name ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company address' . '" ' . $required . ' value="' . ($company->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company phone' . '" ' . $required . ' value="' . ($company->contact_phone ?? null) . '"></div>';
                            break;
                        case 'signature':
                            if ($company->signature)
                                $replaced[] = "<div style='text-align: center;'><img src='" . $company->signature . "' alt='signature'></div>";
                            else
                                $replaced[] = "<div style='text-align: center;'>No company signature available</div>";
                            break;
                    }
                    break;
                case 'image':
                    $foundIdx = array_search($json->image, array_column($images, 'id'), false);
                    if ($foundIdx !== false)
                        $replaced[] = "<div style='text-align: center;'><img class='img-fluid' src='" . $this->getTemporaryFile($images[$foundIdx]['url']) . "' alt='image'></div>";
                    break;
                default:
                    $replaced[] = "{{Error}}";
                    break;
            }
        }

        // Replace special characters for regular expressions
        foreach ($result[0] as $i => $item) {
            $result[0][$i] = "/" . preg_quote ($item, '/') . "/";
        }

        return [
            "html" => preg_replace($result[0], $replaced, $paperwork->template, 1),
            "canvases" => $canvases,
            "validation" => $validation,
        ];
    }

    public function pdf(Request $request, int $id, int $related_id, $driver = null)
    {
        $broker_id = $driver->broker ?? session('broker');
        $paperwork = Paperwork::where(function ($q) use ($broker_id) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) use ($broker_id) {
                    $q->where('id', $broker_id);
                });
            }
        })
            ->findOrFail($id);
        $template = PaperworkTemplate::where('paperwork_id', $id)
            ->where('related_id', $related_id)
            ->first();

        $replaced = [];

        $vars = $this->renderHtmlVars($paperwork->template, $related_id, false, $driver);
        $matches = $vars["matches"];
        $replacements = $vars["replacements"];
        //$carrier = $vars["carrier"];
        //$driver = $vars["driver"];
        $company = $vars["company"];
        $result = $vars["result"];

        $images = $paperwork->images->toArray();
        $filled = $template->filled_template;
        if (!is_array($filled))
            $filled = json_decode($filled);
        foreach ($result[0] as $idx => $element) {
            $formatted = preg_replace($matches, $replacements, $element);
            $json = json_decode($formatted);
            $type = $this->getFormattedJsonType($json);

            switch ($type) {
                case "text":
                case "date":
                    $replaced[] = "<strong>$filled[$idx]</strong>";
                    break;
                case 'radio':
                    $replaced[] = "\r\n<div><h4 class='mt-2'>$json->text</h4>\r\n<div>$filled[$idx]</div></div>";
                    break;
                case 'signature':
                    $replaced[] = "\r\n<div style='text-align: center;'><img src='" . $this->getTemporaryFile($filled[$idx]) . "' alt='signature'></div>";
                    break;
                case 'company':
                    if ($json->company === 'signature')
                        $replaced[] = "\r\n<div style='text-align: center;'><img src='" . $company->signature . "' alt='company signature'></div>";
                    break;
                case 'image':
                    $foundIdx = array_search($json->image, array_column($images, 'id'), false);
                    if (isset($images[$foundIdx]))
                        $replaced[] = "<div style='text-align: center;'><img class='img-fluid' src='" . $this->getTemporaryFile($images[$foundIdx]['url']) . "' alt='image'></div>";
                    break;
            }
        }
        // Replace special characters for regular expressions
        foreach ($result[0] as $i => $item) {
            $result[0][$i] = "/" . preg_quote ($item) . "/";
        }

        $html = str_replace("\r\n", "<br />", "<h1 style='text-align: center;'>$paperwork->name</h1>" . preg_replace($result[0], $replaced, $paperwork->template, 1));
        $title = $paperwork->name;

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/app/logos/logo.png') . ' alt="Logo"></div>');
        $html = view('exports.paperwork.template', compact('title', 'html'));
        $mpdf->AddPage('', // L - landscape, P - portrait
            '', '', '', '',
            5, // margin_left
            5, // margin right
            22, // margin top
            22, // margin bottom
            3, // margin header
            0); // margin footer
        $mpdf->WriteHTML($html);
        return $mpdf->Output();
    }
}
