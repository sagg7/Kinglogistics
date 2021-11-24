<?php

namespace App\Http\Controllers;

use App\Mail\SendPaperworkCompletionNotification;
use App\Models\Broker;
use App\Models\Carrier;
use App\Models\Driver;
use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkImage;
use App\Models\PaperworkTemplate;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use App\Traits\Storage\FileUpload;
use App\Traits\Storage\S3Functions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;

class PaperworkController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, FileUpload, S3Functions, PaperworkFilesFunctions;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

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
            "categories" => [null => 'Initial', 'orientation' => 'Orientation'],
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
        return DB::transaction(function () use ($request, $id) {
            if ($id)
                $paperwork = Paperwork::findOrFail($id);
            else
                $paperwork = new Paperwork();

            $paperwork->name = $request->name;
            $paperwork->type = $request->type;
            $paperwork->shipper_id = $request->shipper_id;
            $paperwork->category = $request->category;
            $paperwork->required = $request->required ?? null;
            $paperwork->template = $request->template ?? null;
            if (($paperwork->file || $request->template) && $request->file)
                $this->deleteFile($paperwork->file);
            $paperwork->file = $request->file ? $this->uploadFile($request->file, "paperworkTemplate" . md5(Carbon::now())) : null;
            $paperwork->save();

            if ($request->images) {
                $images = [];
                $number = $paperwork->images()->latest()->value('number') ?: 0;
                foreach ($request->images as $image) {
                    $number++;
                    $url = $this->uploadImage($image, "paperworkImages/$paperwork->id", 85);
                    $images[] = new PaperworkImage([
                        'url' => $url,
                        'number' => $number,
                    ]);
                }
                $paperwork->images()->saveMany($images);
            }

            return $paperwork;
        });
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
        $paperwork = Paperwork::with('images')->findOrFail($id);
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
     * @return array
     */
    public function destroy($id): array
    {
        $paperwork = Paperwork::findOrFail($id);
        if ($paperwork->file)
            $this->deleteFile($paperwork->file);
        return ['success' => $paperwork->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function deleteImage(Request $request): array
    {
        return DB::transaction(function () use ($request) {
            $image = PaperworkImage::findOrFail($request->id);
            $this->deleteFile($image->url);
            return ['success' => $image->delete()];
        });
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

        return $this->multiTabSearchData($query, $request);
    }

    /**
     * @param $type
     * @param $related_id
     */
    private function checkPaperworkCompletion($type, $related_id): void
    {
        DB::transaction(function () use ($type, $related_id) {
            // Get the mail from driver or carrier to notify seller if the paperwork has been completed
            switch ($type) {
                case 'driver':
                    $user = Driver::with('carrier.seller')->find($related_id);
                    // If the driver has previously completed the paperwork, return
                    if ($user->completed_paperwork)
                        return;
                    $email = $user->carrier->seller->email;
                    $title = "The driver \"$user->name\" has completed its paperwork.";
                    break;
                case 'carrier':
                    $user = Carrier::with('seller')->find($related_id);
                    // If the carrier has previously completed the paperwork, return
                    if ($user->completed_paperwork)
                        return;
                    $email = $user->seller->email;
                    $title = "The carrier \"$user->name\" has completed its paperwork";
                    break;
                default:
                    return;
            }
            // If no email was found then return and don't check completion
            if (!$email)
                return;
            // Get the current related paperwork type model
            $model = "App\Models\\" . ucfirst($type);
            // Get the user
            $modelUser = $model::find($related_id);

            // Get the templates and files that should be filled to this type
            $paperwork = $this->getPaperworkByType($type, $modelUser->id);
            $paperworkFiles = $paperwork['filesUploads'];
            $paperworkTemplates = $paperwork['filesTemplates'];

            // Get the filled paperwork
            $files = $this->getFilesPaperwork($paperworkFiles, $modelUser->id);
            $templates = $this->getTemplatesPaperwork($paperworkTemplates, $modelUser->id);

            // Check through the paperwork files
            foreach ($paperworkFiles as $paperworkFile) {
                // If the file is required, check if it has been completed
                if ($paperworkFile->required) {
                    $index = array_search($paperworkFile->id, array_column($files, 'paperwork_id'), false);
                    // If not, return
                    if ($index === false) {
                        return;
                    }
                }
            }
            // Check through the paperwork templates
            foreach ($paperworkTemplates as $paperworkTemplate) {
                // If the file is required, check if it has been completed
                if ($paperworkTemplate->required) {
                    $index = array_search($paperworkTemplate->id, array_column($templates, 'paperwork_id'), false);
                    // If not, return
                    if ($index === false) {
                        return;
                    }
                }
            }

            $user->completed_paperwork = 1;
            $user->save();

            // If it gets to this point, the paperwork is completed and must send and email to the seller
            $content = "Check out the progress through this link";
            $route = route("$type.edit", $related_id);
            $params = compact('title', 'content', 'route');
            //return view('mails.notification', $params);
            Mail::to($email)->send(new SendPaperworkCompletionNotification($params));
        });
    }

    public function storeFiles(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $result = [];
            if ($request->file) {
                $fileCount = 1;
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

                    if (count($request->file) === $fileCount) {
                        $paperwork->load('parentPaperwork');
                        $this->checkPaperworkCompletion($paperwork->parentPaperwork->type, $request->related_id);
                    }
                    $fileCount++;
                }
            }

            return ['success' => true, 'data' => $result];
        });
    }

    public function showTemplate(Request $request, int $id, int $related_id)
    {
        $paperwork = Paperwork::with('images')->findOrFail($id);
        $data = $this->templateToHtml($paperwork, $related_id);

        $params = compact('paperwork', 'id', 'related_id', 'data');
        return view("paperwork.templates.show", $params);
    }

    /**
     * @param string $template
     * @param null $related_id
     * @param false $simpleVars
     * @return array
     */
    private function renderHtmlVars(string $template, $related_id = null, bool $simpleVars = false): array
    {
        preg_match_all("/{{[^}]*}}/", $template, $result);

        $matches = ["/{{\"signature\"}}/","/{{\"date\"}}/", "/{{/", "/}}/", "/,\s/", "/\"validate\"/"];
        $replacements = ["{{\"signature\":true}}","{{\"date\":true}}", "{", "}", ",", "\"validate\":true"];

        if (!$simpleVars) {
            $carrier = null;
            $company = Broker::find($this->broker_id);
            if (auth()->guard('carrier')->check()) {
                $carrier = auth()->user();
            } else if (auth()->guard('driver')->check()) {
                $carrier = auth()->user()->load('carrier')->carrier;
            } else if (auth()->guard('web')->check()) {
                $carrier = Carrier::find($related_id);
            }
            $driver = null;
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

    private function getFormattedJsonType($json)
    {
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

    public function templateToHtml(Paperwork $paperwork, $related_id = null)
    {
        $replaced = [];
        $canvases = [];
        $validation = [];

        $vars = $this->renderHtmlVars($paperwork->template, $related_id);
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
                            '<input type="checkbox" value="signed" id="' . $inputName .'" required>'.
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
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Carrier name' . '" required value="' . ($carrier->name ?? null) . '"></div>';
                            break;
                        case 'owner':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Owner name' . '" required value="' . ($carrier->owner ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Carrier address' . '" required value="' . ($carrier->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Carrier phone' . '" required value="' . ($carrier->phone ?? null) . '"></div>';
                            break;
                    }
                    break;
                case 'driver':
                    switch ($json->driver) {
                        case 'name':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver name' . '" required value="' . ($driver->name ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver address' . '" required value="' . ($driver->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Driver phone' . '" required value="' . ($driver->phone ?? null) . '"></div>';
                            break;
                    }
                    break;
                case 'company':
                    switch ($json->company) {
                        case 'name':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company name' . '" required value="' . ($company->name ?? null) . '"></div>';
                            break;
                        case 'address':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company address' . '" required value="' . ($company->address ?? null) . '"></div>';
                            break;
                        case 'phone':
                            $replaced[] = '<div class="form-group d-inline-block m-0"><input class="form-control" type="' . $type . '" ' . $inputName . ' placeholder="' . 'Company phone' . '" required value="' . ($company->contact_phone ?? null) . '"></div>';
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

    public function storeTemplate(Request $request, int $id, int $related_id)
    {
        return DB::transaction(function () use ($request, $id, $related_id) {
            $paperwork = Paperwork::findOrFail($id);
            $template = PaperworkTemplate::where('related_id', $related_id)
                ->where('paperwork_id', $id)
                ->first()
                ?: new PaperworkTemplate();

            $vars = $this->renderHtmlVars($paperwork->template, $related_id);
            $matches = $vars["matches"];
            $replacements = $vars["replacements"];
            $carrier = $vars["carrier"];
            $driver = $vars["driver"];
            $result = $vars["result"];

            $correctAnswers = 0;
            $totalAnswers = 0;
            $template_filled = [];
            $signature = null;
            switch ($paperwork->type) {
                case 'driver':
                    $signature = $driver->signature;
                    break;
                case 'carrier':
                    $signature = $carrier->signature;
                    break;
            }
            foreach ($result[0] as $idx => $element) {
                $formatted = preg_replace($matches, $replacements, $element);
                $json = json_decode($formatted);
                $type = $this->getFormattedJsonType($json);
                $type = $this->getGeneralType($type, $json);
                $reqAnswer = null;
                switch ($type) {
                    case 'text':
                    case 'carrier':
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
                        if (!$signature) {
                            $signature = $this->uploadImage($reqAnswer, "paperworkTemplates/$paperwork->type/$related_id");
                            switch ($paperwork->type) {
                                case 'driver':
                                    $driver->signature = $signature;
                                    $driver->save();
                                    break;
                                case 'carrier':
                                    $carrier->signature = $signature;
                                    $carrier->save();
                                    break;
                            }
                        }
                        $reqAnswer = $signature;
                        break;
                    case 'date':
                        $reqAnswer = $request["date-$idx"];
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
            $template->ip = $request->ip();
            $template->device = $request->header('user-agent');
            $template->save();

            $guard = null;
            if (auth()->guard('web')->check())
                $guard = 'web';
            else if (auth()->guard('carrier')->check())
                $guard = 'carrier';
            else if (auth()->guard('driver')->check())
                $guard = 'driver';

            $this->checkPaperworkCompletion($paperwork->type, $related_id);

            if (session('fillDocumentation')) {
                return redirect("/documentation");
            }
            switch ($paperwork->type) {
                case 'carrier':
                    switch ($guard) {
                        case 'carrier':
                            return redirect()->route('carrier.profile');
                        case 'web':
                            return redirect()->route('carrier.edit', $related_id);
                    }
                    break;
                case 'driver':
                    switch ($guard) {
                        case 'driver':
                            return redirect()->route('driver.profile');
                        case 'carrier':
                        case 'web':
                            return redirect()->route('driver.edit', $related_id);
                    }
                    break;
                case 'trailer':
                    return redirect()->route('trailer.edit', $related_id);
                case 'truck':
                    return redirect()->route('truck.edit', $related_id);
                default:
                    return redirect()->route("$paperwork->type.index");
            }
        });
    }

    public function pdf(Request $request, int $id, int $related_id)
    {
        $paperwork = Paperwork::find($id);
        $template = PaperworkTemplate::where('paperwork_id', $id)
            ->where('related_id', $related_id)
            ->first();

        $replaced = [];

        $vars = $this->renderHtmlVars($paperwork->template, $related_id);
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
            $type = $this->getGeneralType($type, $json);

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

        $html = "<h1 style='text-align: center;'>$paperwork->name</h1>" . preg_replace($result[0], $replaced, $paperwork->template, 1);
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
