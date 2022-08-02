<?php

namespace App\Http\Controllers;

use App\Mail\SendPaperworkCompletionNotification;
use App\Mail\SendNotificationPaperwork;
use App\Models\Broker;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Trailer;
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
            "types" => [null => '', 'carrier' => session('renames') ? session('renames')->carrier ?? 'Carrier' : 'Carrier', 'driver' => 'Drivers', 'trailer' => 'Trailers', 'truck' => 'Trucks', 'staff' => 'Staff'],
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
            if ($id) {
                $paperwork = Paperwork::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->findOrFail($id);
            } else {
                $paperwork = new Paperwork();
                $paperwork->broker_id = session('broker');
            }

            $paperwork->name = $request->name;
            $paperwork->type = $request->type;
            $paperwork->shipper_id = $request->shipper_id;
            $paperwork->category = $request->category;
            $paperwork->required = $request->required ?? null;
            $paperwork->share = $request->share ?? null;
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
        $paperwork = Paperwork::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with('images')->findOrFail($id);
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
        $paperwork = Paperwork::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
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
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
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
                    $email = ($user->carrier->seller) ?? null;
                    $title = "The driver \"$user->name\" has completed its paperwork.";
                    break;
                case 'carrier':
                    $user = Carrier::with('seller')->find($related_id);
                    // If the carrier has previously completed the paperwork, return
                    if ($user->completed_paperwork)
                        return;
                    $email = ($user->seller) ? $user->seller->email : null;
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
                        ->where(function ($q) {
                            if (auth()->guard('web')->check()) {
                                $q->whereHas('parentPaperwork', function ($q) {
                                    $q->whereHas('broker', function ($q) {
                                        $q->where('id', session('broker'));
                                    });
                                });
                            }
                        })
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
            } else if ($request->expiration_date) {
                foreach ($request->expiration_date as $i => $item) {
                    if ($item) {
                        $paperwork = PaperworkFile::where('paperwork_id', $i)
                            ->where(function ($q) {
                                if (auth()->guard('web')->check()) {
                                    $q->whereHas('parentPaperwork', function ($q) {
                                        $q->whereHas('broker', function ($q) {
                                            $q->where('id', session('broker'));
                                        });
                                    });
                                }
                            })
                            ->where('related_id', $request->related_id)
                            ->first();
                        if ($paperwork) {
                            if (!$paperwork->expiration_date || (!Carbon::parse($paperwork->expiration_date)->isSameDay(Carbon::parse($request->expiration_date[$i]))))
                                $paperwork->expiration_date = $request->expiration_date[$i];
                            $paperwork->save();
                        }
                    }
                }
            }

            return ['success' => true, 'data' => $result];
        });
    }

    public function showTemplate(Request $request, int $id, int $related_id)
    {
        $paperwork = Paperwork::where(function ($q) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            }
        })
            ->with('images')->findOrFail($id);
        $data = $this->templateToHtml($paperwork, $related_id);

        $params = compact('paperwork', 'id', 'related_id', 'data');
        return view("paperwork.templates.show", $params);
    }

    public function storeTemplate(Request $request, int $id, int $related_id)
    {
        return DB::transaction(function () use ($request, $id, $related_id) {
            $paperwork = Paperwork::where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
                ->findOrFail($id);
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
            $date = $vars["date"];

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
                            if (trim($json->answers[0]) === $reqAnswer)
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
                        $reqAnswer = $date;
                        break;
                }
                $template_filled[] = $reqAnswer;
            }
            if ($totalAnswers > 0) {
                $validationTotal = ($correctAnswers * 100) / $totalAnswers;
                if ($validationTotal < 70)
                    return redirect()->back()->with('error', 'Your score wasn\'t enough to proceed, you may try to answer again');
            }
            //dd($template_filled);

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
                case 'staff':
                    return redirect()->route('user.index');
                default:
                    return redirect()->route("$paperwork->type.index");
            }
        });
    }
}

