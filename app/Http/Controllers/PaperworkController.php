<?php

namespace App\Http\Controllers;

use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkTemplate;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Storage\FileUpload;
use App\Traits\Storage\S3Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;

class PaperworkController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, FileUpload, S3Functions;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'template' => ['sometimes', 'string'],
            'type' => ['required'],
        ]);
    }

    private function createdEditParams()
    {
        return [
            "mode" => ['Simple', 'Advanced'],
            "types" => [null => '', 'carrier' => 'Carriers', 'driver' => 'Drivers', 'trailer' => 'Trailers', 'truck' => 'Trucks'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('paperwork.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createdEditParams();
        return view('paperwork.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('paperwork.index');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Paperwork
     */
    private function storeUpdate(Request $request, $id = null)
    {
        if ($id)
            $paperwork = Paperwork::findOrFail($id);
        else
            $paperwork = new Paperwork();

        $paperwork->name = $request->name;
        $paperwork->type = $request->type;
        $paperwork->required = $request->required ?? null;
        $paperwork->template = $request->template ?? null;
        $paperwork->save();

        return $paperwork;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $paperwork = Paperwork::findOrFail($id);
        $paperwork->mode = $paperwork->template ? 1 : 0;
        $params = compact('paperwork') + $this->createdEditParams();
        return view('paperwork.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('paperwork.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paperwork = Paperwork::findOrFail($id);

        if ($paperwork)
            return ['success' => $paperwork->delete()];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Paperwork::select([
            "paperwork.id",
            "paperwork.name",
            "paperwork.type",
            "paperwork.required",
        ])
            ->where('type', $type);

        return $this->simpleSearchData($query, $request, "where", true);
    }

    public function storeFiles(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $result = [];
            if ($request->file) {
                foreach ($request->file as $i => $file) {
                    $paperwork = PaperworkFile::where('paperwork_id', $i)
                        ->where('related_id', $request->related_id)
                        ->first();
                    if (!$paperwork)
                        $paperwork = new PaperworkFile();
                    else
                        $this->deleteFile($paperwork->getRawOriginal('url'));
                    $paperwork->paperwork_id = $i;
                    $paperwork->related_id = $request->related_id;
                    $paperwork->expiration_date = $request->expiration_date[$i];
                    $paperwork->url = $this->uploadFile($file, "paperwork/$request->type/$request->related_id/$i");
                    $paperwork->save();
                    $paperwork->file_name = $paperwork->getFileNameAttribute();

                    $result[] = $paperwork;
                }
            }

            return ['success' => true, 'data' => $result];
        });
    }

    public function showTemplate(Request $request, int $id, int $related_id)
    {
        $paperwork = Paperwork::find($id);
        $data = $this->templateToHtml($paperwork->template);

        $params = compact('paperwork', 'id', 'related_id', 'data');
        return view("paperwork.templates.show", $params);
    }

    public function templateToHtml(string $template)
    {
        preg_match_all("/{{[^}]*}}/", $template, $result);

        $replaced = [];
        $matches = ["/{{/", "/}}/", "/,\s/", "/\"validate\"/", "/\"signature\"/"];
        $replacements = ["{", "}", ",", "\"validate\":true", "\"signature\":true"];
        $canvases = [];
        $validation = [];
        foreach ($result[0] as $idx => $element) {
            $formatted = preg_replace($matches, $replacements, $element);
            $json = json_decode($formatted);
            if (isset($json->answers))
                $type = "radio";
            else if (isset($json->signature))
                $type = "signature";
            else
                $type = "text";
            $inputName = 'name="input-' . $idx .'"';
            switch ($type) {
                case "text":
                    $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . $json->text . '" required></div>';
                    break;
                case 'radio':
                    $html = "<h4 class='m-0'>$json->text</h4>\r\n";
                    if (isset($json->validate))
                        shuffle($json->answers);
                    foreach ($json->answers as $i => $answer) {
                        $radioId = 'input-' . $idx . "-" . $i;
                        $html .= '<input type="' . $type . '" ' . $inputName . ' id="' . $radioId . '" value="' . $answer . '" required><label class="col-form-label" for="' . $radioId . '">' . $answer . "</label>\r\n";
                    }
                    $replaced[] = $html;
                    if (isset($json->validate))
                        $validation[] = "input-$idx";
                    break;
                case 'signature':
                    $canvasId = 'signature-' . $idx;
                    $replaced[] = '<div class="form-group text-center">' .
                        '<label class="col-form-label" for="' . $canvasId . '">Signature</label>' .
                        '<div>' .
                        '<canvas class="d-block mx-auto" id="' . $canvasId . '"></canvas>' .
                        '<button type="button" class="btn btn-outline-danger mt-1">Clear</button>' .
                        '</div>' .
                        '</div>';
                    $canvases[] = $canvasId;
                    break;
                default:
                    $replaced[] = "{{Error}}";
                    break;
            }
        }
        // Replace special characters for regular expressions
        foreach ($result[0] as $i => $item) {
            $result[0][$i] = "/" . preg_quote ($item) . "/";
        }

        return [
            "html" => preg_replace($result[0], $replaced, $template, 1),
            "canvases" => $canvases,
            "validation" => $validation,
        ];
    }

    public function storeTemplate(Request $request, int $id, int $related_id)
    {
        return DB::transaction(function () use ($request, $id, $related_id) {
            $paperwork = Paperwork::findOrFail($id);
            $template = new PaperworkTemplate();

            preg_match_all("/{{[^}]*}}/", $paperwork->template, $result);
            $matches = ["/{{/", "/}}/", "/,\s/", "/\"validate\"/", "/\"signature\"/"];
            $replacements = ["{", "}", ",", "\"validate\":true", "\"signature\":true"];
            $correctAnswers = 0;
            $totalAnswers = 0;
            $template_filled = [];
            foreach ($result[0] as $idx => $element) {
                $formatted = preg_replace($matches, $replacements, $element);
                $json = json_decode($formatted);
                if (isset($json->answers))
                    $type = "radio";
                else if (isset($json->signature))
                    $type = "signature";
                else
                    $type = "text";
                $reqAnswer = null;
                switch ($type) {
                    case 'text':
                        $reqAnswer = $request["input-$idx"];
                        break;
                    case 'radio':
                        $reqAnswer = $request["input-$idx"];
                        if (isset($json->validate)) {
                            $totalAnswers++;
                            if ($json->answers[0] === $reqAnswer)
                                $correctAnswers++;
                        }
                        break;
                    case 'signature':
                        $reqAnswer = $request["signature-$idx"];
                        $reqAnswer = $this->uploadImage($reqAnswer, "paperworkTemplates/$paperwork->type/$related_id");
                        break;
                }
                $template_filled[] = $reqAnswer;
            }
            if ($totalAnswers > 0) {
                $validationTotal = ($correctAnswers * 100) / $totalAnswers;
                if ($validationTotal < 70)
                    return redirect()->back()->with('error', 'Your score wasn\'t enough to proceed, you may try to answer again');
            }

            $template->paperwork_id = $paperwork->id;
            $template->related_id = $related_id;
            $template->filled_template = json_encode($template_filled);
            $template->save();

            if (auth()->guard('web')->check())
                return redirect()->route('driver.profile');
            else
                return redirect()->route("$paperwork->type.index");
        });
    }

    public function pdf(Request $request, int $id, int $related_id)
    {
        $paperwork = Paperwork::find($id);
        $template = PaperworkTemplate::where('paperwork_id', $id)
            ->where('related_id', $related_id)
            ->first();

        preg_match_all("/{{[^}]*}}/", $paperwork->template, $result);

        // Replace special characters for regular expressions
        foreach ($result[0] as $i => $item) {
            $result[0][$i] = "/" . preg_quote ($item) . "/";
        }

        $filled = $template->filled_template;
        if (!is_array($filled))
            $filled = json_decode($filled);

        preg_match_all("/{{[^}]*}}/", $paperwork->template, $result);
        $matches = ["/{{/", "/}}/", "/,\s/", "/\"validate\"/", "/\"signature\"/"];
        $replacements = ["{", "}", ",", "\"validate\":true", "\"signature\":true"];
        $replaced = [];
        foreach ($result[0] as $idx => $element) {
            $formatted = preg_replace($matches, $replacements, $element);
            $json = json_decode($formatted);
            if (isset($json->answers))
                $type = "radio";
            else if (isset($json->signature))
                $type = "signature";
            else
                $type = "text";
            switch ($type) {
                case "text":
                    $replaced[] = "<strong>$filled[$idx]</strong>";
                    break;
                case 'radio':
                    $replaced[] = "\r\n<div><h4 class='mt-2'>$json->text</h4>\r\n<p>$filled[$idx]</p></div>";
                    break;
                case 'signature':
                    $replaced[] = "\r\n<div style='text-align: center;'><img src='" . $this->getTemporaryFile($filled[$idx]) . "' alt='signature'></div>";
                    break;
            }
        }
        // Replace special characters for regular expressions
        foreach ($result[0] as $i => $item) {
            $result[0][$i] = "/" . preg_quote ($item) . "/";
        }

        $html = "<h1 style='text-align: center;'>$paperwork->name</h1>" . preg_replace($result[0], $replaced, $paperwork->template, 1);
        $title = $paperwork->name;

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/logo.png') . ' alt="Logo"></div>');
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
