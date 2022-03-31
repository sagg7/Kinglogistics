<?php

namespace App\Http\Controllers;

use App\Events\LoadUpdate;
use App\Exports\LoadsExport;
use App\Models\LoadType;
use App\Exports\TemplateExport;
use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Load;
use App\Models\LoadLog;
use App\Models\LoadStatus;
use App\Models\Shipper;
use App\Models\Trip;
use App\Models\Truck;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\GenerateLoads;
use App\Traits\Storage\S3Functions;
use App\Traits\Turn\DriverTurn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Storage\FileUpload;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use App\Jobs\ProcessDeleteFileDelayed;
use App\Exports\CompareLoadsErrorsExport;
use App\Imports\CompareLoadsImport;
use Illuminate\Database\Eloquent\Relations\Relation;
use \Foo\Bar;
use Illuminate\Support\Facades\Validator;


class LoadController extends Controller
{
    use GenerateLoads, GetSelectionData, GetSimpleSearchData, DriverTurn, FileUpload, S3Functions;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'jobs' => [null => 'Select'] + Trip::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->pluck('name', 'id')->toArray(),
            'shippers' => [null => 'Select'] + Shipper::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->pluck('name', 'id')->toArray(),
            'available_drivers' => [null => 'Select'] + Driver::where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                } else if (auth()->guard('shipper')->check()) {
                    $q->whereHas('shippers', function ($q) {
                        $q->where('shipper_id', auth()->user()->id);
                    });
                }
            })
                ->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('loads.index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDispatch()
    {
        $params = $this->createEditParams();
        return view('loads.indexDispatch', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('loads.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['date'] = $request->date_submit;


        $shipper = auth()->guard('shipper')->check() ? auth()->user()->id : $request->shipper_id;
        $data['shipper_id'] = $shipper;

        $this->validator($data)->validate();

        $drivers = AvailableDriver::with('driver')
            ->whereHas('driver', function ($q) use ($shipper) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
                // Filter users by current Turn, check if is morning first else night
                $q->whereHas('turn', function ($r) {
                    $this->filterByActiveTurn($r);
                });
                // The driver must not be inactive
                $q->whereNull('inactive')
                    // and also a truck
                    ->whereHas('truck', function ($r) {
                        // and also a trailer
                        $r->whereHas('trailer');
                    });
                // Then check the relations with the shipper
                $q->where(function ($r) use ($shipper) {
                    // WHERE THE ASSIGNED TRAILER TO THE TRUCK THAT BELONGS TO THE DRIVER IS OWNED BY THE SHIPPER
                    $r->whereHas('shippers', function ($q) use ($shipper) {
                        $q->where('id', $shipper);
                    })
                        ->orWhereHas('truck', function ($s) use ($shipper) {
                            $s->whereHas('trailer', function ($t) use ($shipper) {
                                $t->whereHas('shippers', function ($u) use ($shipper) {
                                    $u->where('shipper_id', $shipper);
                                });
                            });
                        });
                });
                // The carrier must be active
                $q->whereHas("carrier", function ($q) {
                    $q->whereNull("inactive");
                });
            })
            ->take($request->load_number)
            ->get();

        DB::transaction(function () use ($request, $data, $drivers) {
            $load_log = new LoadLog();
            $load_log->user_id = auth()->user()->id;
            $load_log->quantity = $request->load_number;
            $load_log->type = auth()->guard('shipper')->check() ? 'shipper' : 'user';
            $load_log->save();
            $data['load_log_id'] = $load_log->id;
            $control_number = $request->control_number;
            $rest = substr($control_number, -1);
            $rest2 = substr($control_number, -2, 1);
            $rest3 = substr($control_number, -3, 1);
            $patron = '/([0-9])+$/';


            if (preg_match($patron, $rest)) {
                if (preg_match($patron, $rest2)) {
                    if (preg_match($patron, $rest3)) {
                        $control_number_int =  substr($control_number, -3);
                        $control_number_str = substr($control_number, 0, -3);
                    } else {
                        $control_number_int = substr($control_number, -2);
                        $control_number_str = substr($control_number, 0, -2);
                    }
                } else {
                    $control_number_int = $rest;
                    $control_number_str = substr($control_number, 0, -1);
                }
            } else {
                $control_number_str = $control_number;
                $control_number_int = 0;
            }


            for ($i = 0; $i < $request->load_number; $i++) {
                if (isset($request->driver_id)) { //temporary
                    $data['driver_id'] = $request->driver_id;
                    if ($data['notes'] === "finished") {
                        $data['status'] = 'finished';
                    } else {
                        $data['status'] = 'accepted';
                    }
                } else {
                    // Assign available drivers to load
                    $data['driver_id'] = $drivers[$i]->driver_id ?? null;
                    // If driver was assigned, set status as requested, else set status as unallocated to wait for driver
                    $data['driver_id'] ? $data['status'] = 'accepted' : $data['status'] = 'unallocated';
                }
                $data['control_number'] = $control_number_str . $control_number_int;

                $data['broker_id'] = session('broker');
                $load = $this->storeUpdate($data);

                $control_number_int++;

                event(new LoadUpdate($load));
            }
        });

        if ($request->ajax()) {
            return ['success' => true];
        }

        return redirect()->route('load.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Load $load
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $load = Load::with('driver', 'shipper')
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
            })
            ->find($id);
        $params = compact('load') + $this->createEditParams();
        return view('loads.show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $load = Load::with('driver')
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->with('load_type:id,name', 'trip:id,name')
            ->find($id);
        $params = compact('load') + $this->createEditParams();
        return view('loads.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id)
    {
        /**
         * THERE SHOULD BE NO CASE TO UPDATE
         */
        $data = $request->all();
        $data['date'] = $request->date_submit;
        $data['broker_id'] = session('broker');
        $this->validator($data)->validate();
        $this->storeUpdate($data, $id);

        return redirect()->route('load.index');
        //return abort(404);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function partialUpdate(Request $request, int $id)
    {
        $load = Load::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $load->fill($request->all());
        return $load->update();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function markAsInspected(int $id)
    {
        $load = Load::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $load->inspected = 1;
        return ["success" => $load->update()];
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function unmarkAsInspected(int $id)
    {
        $load = Load::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $load->inspected = null;
        return ["success" => $load->update()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id)
    {
        /**
         * TODO: Return drivers to available drivers
         *
         */

        $load = Load::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        return ['success' => $load->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Load::select([
            'id',
            'name as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where("name", "LIKE", "%$request->search%")
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'driver':
            case 'driver.carrier':
            case 'shipper':
            case 'load_type':
            case 'trip':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            case 'truck':
                $array = [
                    'relation' => $item,
                    'column' => 'number',
                ];
                break;
            default:
                $array = null;
        }

        return $array;
    }




    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if (!$request->start && $request->dateRange) {
            $dates = explode(" - ", $request->dateRange);
            $startA = explode("/", $dates[0]);
            $endA = explode("/", $dates[1]);


            $start = $startA ? Carbon::parse($startA[2] . "-" . $startA[0] . "-" . $startA[1]) : Carbon::now()->startOfMonth();
            $end = $endA ? Carbon::parse($endA[2] . "-" . $endA[0] . "-" . $endA[1])->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        } else {
            $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth()->subMonth();
            $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        }


        $select = [
            "loads.id",
            "loads.date",
            "loads.control_number",
            /*"loads.customer_po",
            "loads.customer_reference",*/
            "loads.origin",
            "loads.destination",
            "loads.driver_id",
            "loads.status",
            "loads.inspected",
            "loads.tons",
            "loads.trip_id",
            "loads.truck_id",
            "loads.mileage",
            "loads.shipper_id",
            "loads.notes",
            "loads.customer_po",
            "loads.load_type_id",
        ];
        $query = Load::with([
            'driver' => function ($q) {
                $q->with([
                    'shift.chassisType',
                    'carrier:id,name',
                ])->withTrashed()
                    ->select([
                        'drivers.id',
                        'drivers.name',
                        'drivers.carrier_id',
                    ]);
            },
            'trip:id,name',
            'truck:id,number',
            'shipper:id,name',
            'load_type:id,name',
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
            ->where(function ($q) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas('shipper', function ($q) {
                        $q->where('shipper_id', auth()->user()->id);
                    });
                }
            })
            ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
            ->where(function ($q) use ($request) {
                if ($request->shipper)
                    $q->where('shipper_id', $request->shipper);
            });
        if ($request->type) {
            if ($request->type == 'active') {
                $query->where('loads.status', '!=', 'finished');
                if (empty($request->sortModel))
                    $query->orderBy('accepted_timestamp', 'desc');
            } else {
                $query->where('loads.status', 'finished');
                if (empty($request->sortModel))
                    $query->orderBy('finished_timestamp', 'desc');
            }
        }
        if (!$request->sortModel) {
            $query->orderByDesc('date');
        }

        if (auth()->guard('web')->check() && $request->dispatch && auth()->user()->can('read-load-dispatch')) {
            $query->with('loadStatus:load_id,to_location_voucher,finished_voucher,accepted_timestamp,finished_timestamp')
                ->whereBetween(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end]);
            $select[] = 'customer_reference';
            $select[] = 'bol';
            $select[] = 'accepted_timestamp';
            $select[] = 'finished_timestamp';
        } else {
            if (isset($request->searchable)) {
                $array = $request->searchable;
                $array[] = 'customer_reference';
                $array[] = 'customer_po';
                $array[] = 'bol';
                $request->searchable = $array;
            }
        }

        switch ($request->search) {
            case "#duplicate":
                $query->where(function ($q) {
                    $q->whereIn('customer_reference', function ($q) {
                        $q->select('customer_reference')
                            ->from('loads')
                            ->where('customer_reference', '!=', '')
                            ->groupBy('customer_reference')
                            ->havingRaw('count(*) > 1');
                    })->orWhereIn('control_number', function ($q) {
                        $q->select('control_number')
                            ->from('loads')
                            ->where('control_number', '!=', '')
                            ->groupBy('control_number')
                            ->havingRaw('count(*) > 1');
                    })->orWhereIn('bol', function ($q) {
                        $q->select('bol')
                            ->from('loads')
                            ->where('bol', '!=', '')
                            ->groupBy('bol')
                            ->havingRaw('count(*) > 1');
                    });
                });
                $request->searchable = null;
                break;
            default:
                break;
        }

        if (str_contains($request->search, 'transit')) {
            $request->search = 'location';
        }

        if (strpos($request->search, '/')) {
            $date = explode('/', $request->search);
            $request->search = $date[2] . '-' . $date[0] . '-' . $date[1];
        }
        $query->select($select);

        if ($request->download) {
            $dquery = $this->multiTabSearchData($query, $request, 'getRelationArray');
            if (count($dquery['rows']) === 0)
                return redirect()->back()->withErrors('There are no loads to generate the document');
            $data = [];
            $now = Carbon::now();
            foreach ($dquery['rows'] as $item) {
                $data[] = [
                    'accepted_timestamp' => $item->accepted_timestamp ? Carbon::createFromFormat('Y-m-d H:i:s', $item->accepted_timestamp)->format('m/d/Y H:i') : null,
                    'finished_timestamp' => $item->finished_timestamp ? Carbon::createFromFormat('Y-m-d H:i:s', $item->finished_timestamp)->format('m/d/Y H:i') : null,
                    'truck' => $item->truck ? $item->truck->number : null,
                    'driver' => $item->driver->name,
                    'carrier' => $item->driver->carrier->id,
                    'control_number' => $item->control_number,
                    'customer_reference' => $item->customer_reference,
                    'bol' => $item->bol,
                    'tons' => $item->tons,
                    'mileage' => $item->mileage,
                    'load_type' => $item->load_type->name,
                    'trip' => $item->trip->name,
                    'customer_po' => $item->customer_po,
                    'shipper' => $item->shipper->name,
                    'status' => ucfirst($item->status),
                    'load_time' =>  $this->msToTime(($item->finished_timestamp ? Carbon::parse($item->accepted_timestamp)->diffInMinutes($item->finished_timestamp) : Carbon::parse($item->accepted_timestamp)->diffInMinutes($now)) * 60 * 1000, false),
                ];
            }
            return (new TemplateExport([
                "data" => $data,
                "headers" => [
                    "Accepted Timestamp", "Finished Timestamp", "Truck #", "Driver", "Carrier", "Control #", "C Reference", "BOL", "Tons", "Milage", "Load Type", "Job",  "PO",
                    "Customer", "Status", "Load Time"
                ],
            ]))->download("Loads - " . Carbon::now()->format('m-d-Y') . ".xlsx");
        }

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    private function msToTime($duration, $showSeconds = true)
    {
        $seconds = floor(($duration / (1000)) % 60);
        $minutes = floor(($duration / (1000 * 60)) % 60);
        $hours = floor(($duration / (1000 * 60 * 60)) % 24);
        $days = floor(($duration / (1000 * 60 * 60 * 24)));

        $hours = ($hours < 10) ? "0$hours" : "$hours";
        $minutes = ($minutes < 10) ? "0$minutes" : "$minutes";
        $secs = "";
        if ($showSeconds)
            $secs = "$seconds s";
        if ($days > 0)
            return "$days d $hours h $minutes m $secs";
        else if ($hours > 0)
            return "$hours h $minutes m $secs";
        else
            return "$minutes m $secs";
    }


    public function replacePhoto(Request $request, $id, $type)
    {

        $load_status = LoadStatus::whereHas('parentLoad', function ($q) {
            $q->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        })
            ->where('load_id', $id)->first();

        $new_voucher = $this->uploadImage($request[json_decode($request->slim[0])->output->field], "loads/$load_status->id", 30);
        if ($type == "to_location") {
            $load_status->to_location_voucher = $new_voucher;
        } else {
            $load_status->finished_voucher = $new_voucher;
        }

        if ($load_status->save()) {
            return [
                "status" => "false",
                "name" => "uid_filename.jpg",
                "path" => "path/uid_filename.jpg"
            ];
        } else {
            return [
                "status" => "success",
                "name" => "uid_filename.jpg",
                "path" => "path/uid_filename.jpg"
            ];
        }
    }

    function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public function loadPhoto($id, $type)
    {
        $load_status = LoadStatus::whereHas('parentLoad', function ($q) {
            $q->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        })
            ->where('load_id', $id)->first();

        $path = "app/public/loads/";
        $this->deleteDirectory($path);

        if ($type == "to_location") {
            $url = $this->getTemporaryFile("$load_status->to_location_voucher");
            $contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            Storage::put("public/loads/" . $load_status->load_id . "/to_location.jpg", $contents);
            return asset("storage/loads/" . $load_status->load_id . "/to_location.jpg");
        } else {
            $url = $this->getTemporaryFile("$load_status->finished_voucher");
            $contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            Storage::put("public/loads/" . $load_status->load_id . "/finished.jpg", $contents);
            return asset("storage/loads/" . $load_status->load_id . "/finished.jpg");
        }
    }

    public function DownloadExcelReport(Request $request)
    {
        // $request->endRow = 0;
        // $request->startRow = 1000;
        $dates = explode(" - ", $request->dateRange);
        $startA = explode("/", $dates[0]);
        $endA = explode("/", $dates[1]);

        $request->merge(["start" => $startA[2] . "-" . $startA[0] . "-" . $startA[1]]);
        $request->merge(["end" => $endA[2] . "-" . $endA[0] . "-" . $endA[1]]);
        $request->merge(["endRow" => 1000]);
        $request->merge(["startRow" => 0]);
        //$result = $this->search($request);
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        $result = Driver::select('name', 'id')
            /* ->whereHas('trailer', function ($q) use ($request) {                      ///checar que todos tengan trailer
                        $q->whereHas('shippers', function ($q) use ($request) {
                            $q->where('id', $request->shipper);
                        });
                    })*/
            ->with('loadStatus', function ($q) use ($start, $end,  $request) {
                $q->whereBetween(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end]);
                if (!empty($request->shipper)) //quitar cuando todos tengan trailer
                    $q->where("shipper_id", "$request->shipper");
            })
            ->orderBy("name");

        return (new LoadsExport($result->get()))->download('Dispatch Report.xlsx');
    }

    public function pictureReport(Request $request)
    {
        $dates = explode(" - ", $request->dateRange);
        $startA = explode("/", $dates[0]);
        $endA = explode("/", $dates[1]);


        $start = $startA ? Carbon::parse($startA[2] . "-" . $startA[0] . "-" . $startA[1]) : Carbon::now()->startOfMonth();
        $end = $endA ? Carbon::parse($endA[2] . "-" . $endA[0] . "-" . $endA[1])->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $select = [
            "loads.id",
            "loads.control_number",
            "loads.origin",
            "loads.destination",
            "loads.driver_id",
            "loads.trip_id",
            "loads.status",
            "loads.inspected",
        ];
        $query = Load::with('driver:id,name')
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->with('trip:id,name')
            ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
            ->whereBetween(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end])
            //->whereNull('inspected')
            ->where(function ($q) use ($request) {
                if ($request->shipper)
                    $q->where('shipper_id', $request->shipper);
            });

        $query->with('loadStatus:load_id,to_location_voucher,finished_voucher,accepted_timestamp,finished_timestamp');
        $select[] = 'customer_reference';
        $select[] = 'bol';
        $select[] = 'accepted_timestamp';
        $select[] = 'finished_timestamp';
        $query->orderBy('finished_timestamp', 'desc');

        $photos = [];
        $loads = [];
        foreach ($query->select($select)->get() as $key => $load) {
            $loads[] = [
                'driverName' => $load->driver->name,
                'job' => $load->trip->name,
                'control_number' => $load->control_number,
                'customer_reference' => $load->customer_reference,
                'bol' => $load->bol,
                'finished' => (isset($load->loadStatus->finished_voucher)) ? $this->getTemporaryFile($load->loadStatus->finished_voucher) : "NO IMAGE",
                'ticket' => (isset($load->loadStatus->to_location_voucher)) ? $this->getTemporaryFile($load->loadStatus->to_location_voucher) : "NO IMAGE",
                'finished_timestamp' => $load->loadStatus->finished_timestamp,
                'inspected' => $load->inspected,
                'status' => $load->status,
            ];
        }

        return view('exports.loads.loadPictures', compact('loads'));
    }

    public function addObservation(Request $request, $id): array
    {
        $load = Load::find($id);
        $load->notes = $request->observation;
        if ($load->save())
            return ['success' => true, 'load' => $load];
        else
            return ['success' => false];
    }

    public function getLoadNote($id)
    {
        $load = Load::find($id);
        return $load->notes;
    }


    public function transferJob(Request $request, $id): array
    {
        $load = Load::find($id);
        $trip = Trip::find($request->trip_id);
        $load->shipper_id = $trip->shipper_id;
        $load->trip_id = $trip->id;
        $load->origin = $trip->trip_origin ? $trip->trip_origin->name : $trip->origin;
        $load->origin_coords = $trip->trip_origin ? $trip->trip_origin->coords : $trip->origin_coords;
        $load->destination = $trip->trip_destination ? $trip->trip_destination->name : $trip->destination;
        $load->destination_coords = $trip->trip_destination ? $trip->trip_destination->coords : $trip->destination_coords;
        $load->customer_name = $trip->customer_name;
        $load->mileage = $trip->mileage;
        $load->rate = $trip->rate->carrier_rate;
        $load->shipper_rate = $trip->rate->shipper_rate;

        if ($load->save())
            return ['success' => true, 'load' => $load];
        else
            return ['success' => false];
    }

    public function finishLoad($id)
    {
        $load = Load::find($id);
        $loadStatus = LoadStatus::where('load_id', $id)->first();

        switch ($load->status) {
            case "unallocated":
            case "requested":
                return ['success' => false, 'message' => "you cannot finish a load without assigning a driver"];
                break;
            case "accepted":
                $loadStatus->loading_timestamp = Carbon::now();
                $loadStatus->to_location_timestamp = Carbon::now();
                $loadStatus->arrived_timestamp = Carbon::now();
                $loadStatus->unloading_timestamp = Carbon::now();
                $loadStatus->finished_timestamp = Carbon::now();
                break;
            case "loading":
                $loadStatus->to_location_timestamp = Carbon::now();
                $loadStatus->arrived_timestamp = Carbon::now();
                $loadStatus->unloading_timestamp = Carbon::now();
                $loadStatus->finished_timestamp = Carbon::now();
                break;
            case "to_location":
                $loadStatus->arrived_timestamp = Carbon::now();
                $loadStatus->unloading_timestamp = Carbon::now();
                $loadStatus->finished_timestamp = Carbon::now();
                break;
            case "arrived":
                $loadStatus->unloading_timestamp = Carbon::now();
                $loadStatus->finished_timestamp = Carbon::now();
                break;
            case "unloading":
                $loadStatus->finished_timestamp = Carbon::now();
                break;
            case "finished":
                return ['success' => false, 'message' => "you cannot finish a finished load"];
                break;
        }
        $load->status = 'finished';
        $load->save();
        $loadStatus->save();
        return ['success' => true, 'load' => $load];
    }

    public function uploadCompareLoadExcel(Request $request)
    {

        $import = new CompareLoadsImport($request->dateRange, $request->shipper);
        Excel::import($import, $request->fileExcel);
        $data = $import->data;

        $result = ['success' => true, 'dataFile' => $data];

        if ($data['errors']) {
            $directory = "temp/xls/" . md5(Carbon::now());
            $path = $directory . "/Compare_Loads_Excel_Errors.xlsx";
            $publicPath = "public/" . $path;
            (new CompareLoadsErrorsExport($data['errors']))->store($publicPath);
            ProcessDeleteFileDelayed::dispatch($directory, true)->delay(now()->addMinutes(1));
            $result['errors_file'] = asset("storage/" . $path);
        }

        return $result;
    }

    public function downloadXLSInternal(Request $request)
    {
//dd(explode(",",str_replace(["[","]"], ["",""], $request->array)));
        $loadsIds = json_decode($request->array);
      
        $load = Load::with([
            'driver' => function ($q) {
                $q->with([
                    'carrier:id,name',
                ])
                    ->select([
                        'drivers.id',
                        'drivers.name',
                        'drivers.carrier_id',
                    ]);
            },
            'truck:id,number',
            'shipper:id,name',
            'load_type:id,name',
            'trip:id,name',
            'loadStatus'
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->whereIn('id', $loadsIds)
            ->get();

        
            $arrayLoad = array();
            
            foreach ($loadsIds  as $key => $arrays) {
                foreach ($load as $keyLoad => $loads) {
                    if ($arrays == $loads->id) {
                        $arrayLoad[] = $loads;
                        break 1;
                    }
                }
            }

            if (count($arrayLoad) === 0)
                return redirect()->back()->withErrors('There are no loads to generate the document');
        $data = [];
        foreach ($arrayLoad as $load) {
            $data[] = [
                'control_number' => $load->control_number,
                'truck_number' => $load->truck ? $load->truck->number  : null,
                'driver_name' => $load->driver ? $load->driver->name : null,
                'carrier' => $load->driver ? $load->driver->carrier->name : null,
                'customer_reference' => $load->customer_reference ? $load->customer_reference : null,
                'bol' => $load->bol ? $load->bol : null,
                'weight' => $load->weight ? $load->weight : null,
                'tons'  => $load->tons ? $load->tons: null, 
                'mileage' => $load->mileage ?  $load->mileage : null, 
                'load_type' =>  $load->load_type->name,
                'job' => $load->trip ? $load->trip->name :null, 
                'PO' => $load->customer_po, 
                'customer_name' => $load->customer_name, 
                'status' => $load->status,  
                'box_type_id_init' => $load->box_type_id_init ? $load->box_type_id_init :null, 
                'box_type_id_end' => $load->box_type_id_end ? $load->box_type_id_end :null, 
                'unallocated_timestamp' => $load->loadStatus ? $load->loadStatus->unallocated_timestamp: null, 
                'requested_timestamp' => $load->loadStatus ? $load->loadStatus->requested_timestamp: null, 
                'accepted_timestamp' => $load->loadStatus ? $load->loadStatus->accepted_timestamp: null, 
                'loading_timestamp' => $load->loadStatus ? $load->loadStatus->loading_timestamp: null, 
                'to_location_timestamp' => $load->loadStatus ? $load->loadStatus->to_location_timestamp: null, 
                'arrived_timestamp' => $load->loadStatus  ? $load->loadStatus->arrived_timestamp: null, 
                'unloading_timestamp' => $load->loadStatus  ? $load->loadStatus->unloading_timestamp: null, 
                'finished_timestamp' => $load->loadStatus  ? $load->loadStatus->finished_timestamp: null, 
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Control #", "Truck #", "Driver", "Carrier", "C Reference", "BOL", "Weight", "Tons", "Milage", "Load Type", "Job", "PO", "Customer","Status",
        "Box Id", "Box id end", "Unallocated Timestamp", "Requested Timestamp", "Accepted Timestamp", "Loading Timestamp","To location Timestamp", "Arrived Timestamp","Unloading Timestamp", "Finished Timestamp" ],
            "formats" => [
                // 'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("Internal loads" . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");

        return $request;
    }


    public function downloadXLSExternal(Request $request)
    {
        $loads = json_decode($request->array);
        $arrayobj = count(get_object_vars($loads));
        if ($arrayobj === 0)
           return redirect()->back()->withErrors('There are no loads to generate the document');
        $data = [];
        foreach ($loads as $key => $row) {
            $data[] = [
                'control_number' => $row[0],
                'truck_number' => $row[1] ? $row[1]  : null,
                'driver_name' => $row[2] ? $row[2] : null,
                'carrier' => $row[3] ? $row[3] : null,
                'customer_reference' => $row[4] ? $row[4]: null,
                'bol' => $row[5] ? $row[5] : null,
                'weight' => $row[6] ? $row[6] : null,
                'tons'  => $row[7] ? $row[7]: null, 
                'mileage' => $row[8] ?  $row[8] : null, 
                'load_type' =>  $row[9],
                'job' => $row[10] ? $row[10] :null, 
                'PO' => $row[11], 
                'customer_name' => $row[12], 
                'status' => $row[13],  
                'box_type_id_init' => $row[14] ? $row[14] :null, 
                'box_type_id_end' => $row[15] ? $row[15] :null, 
                'unallocated_timestamp' =>$row[16] ? $row[16]: null, 
                'requested_timestamp' => $row[17] ? $row[17] : null, 
                'accepted_timestamp' => $row[18] ? $row[18]: null, 
                'loading_timestamp' => $row[19] ? $row[19]: null, 
                'to_location_timestamp' => $row[20] ? $row[20]: null, 
                'arrived_timestamp' => $row[21]  ? $row[21]: null, 
                'unloading_timestamp' => $row[22] ? $row[22]: null, 
                'finished_timestamp' =>$row[23]  ? $row[23]: null, 
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Control #", "Truck #", "Driver", "Carrier", "C Reference", "BOL", "Weight", "Tons", "Milage", "Load Type", "Job", "PO", "Customer","Status",
             "Box Id", "Box id end", "Unallocated Timestamp", "Requested Timestamp", "Accepted Timestamp", "Loading Timestamp","To location Timestamp", "Arrived Timestamp","Unloading Timestamp", "Finished Timestamp" ],
            "formats" => [
                // 'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("External loads" . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
      

        return $request;
    }

    public function downloadTmpXLS()
    {
        $data = [];
        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Control #", "Truck #", "Driver", "Carrier", "C Reference", "BOL", "Weight", "Tons", "Milage", "Load Type", "Job", "PO", "Customer","Status",
             "Box Id", "Box id end", "Unallocated Timestamp", "Requested Timestamp", "Accepted Timestamp", "Loading Timestamp","To location Timestamp",
            "Arrived Timestamp","Unloading Timestamp", "Finished Timestamp" ],
        ]))->download("Loads" . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }

    // private function validator(Load $load, int $id = null)
    // {
    //     return Validator::make($load, [
    //         'load_number' => ['sometimes', 'numeric', 'min:1', 'max:999'],
    //         'shipper_id' => ['sometimes', 'exists:shippers,id'],
    //         'trip_id' => ['required', 'exists:trips,id'],
    //         'load_type_id' => ['required', 'exists:load_types,id'],
    //         'driver_id' => ['nullable', 'exists:drivers,id'],
    //         'date' => ['required', 'date'],
    //         'control_number' => ['required', 'string', 'max:255'],
    //         'origin' => ['required', 'string', 'max:255'],
    //         'origin_coords' => ['required', 'string', 'max:255'],
    //         'destination' => ['required', 'string', 'max:255'],
    //         'destination_coords' => ['required', 'string', 'max:255'],
    //         'customer_name' => ['required', 'string', 'max:255'],
    //         'customer_po' => ['nullable', 'string', 'max:255'],
    //         'customer_reference' => ['nullable', 'string', 'max:255'],
    //         'tons' => ['nullable', 'string', 'max:255'],
    //         // 'silo_number' => ['nullable', 'string', 'max:255'],
    //         // 'container' => ['nullable', 'string', 'max:255'],
    //         'weight' => ['nullable', 'numeric'],
    //         'mileage' => ['nullable', 'numeric'],
    //         'status' => ['sometimes', 'required'],
    //     ], [
    //         'origin_coords.required' => 'The origin map location is required',
    //         'destination_coords.required' => 'The destination map location is required',
    //     ], [
    //         'shipper_id' => 'customer',
    //         'trip_id' => 'trip',
    //         'load_type_id' => 'load type',
    //         'driver_id' => 'driver',
    //     ]);
    // }

    public function createLoadsExternal(Request $request){
        $loadsExt = json_decode($request->array);
        $loadExtArry = get_object_vars($loadsExt[0]);
        $arrayobj = count($loadsExt);
        if ($arrayobj === 0)
           return redirect()->back()->withErrors('There are no loads to generate the document');
       
           foreach ($loadExtArry as $key => $row) {
            if($row[1]){$truck = Truck::where('number','LIKE','%'.$row[1].'%')->first();}
            if($row[2]){$driver = Driver::where('name','LIKE','%'.$row[2].'%')->first(); }
            // LoadType and Trip can't be null
            if($row[9]){$loadType = LoadType::where('name','LIKE','%'.$row[9].'%')->first(); }
            if($row[10]){$trip = Trip::where('name','LIKE','%'.$row[10].'%')->first(); }
                $load_logs = new LoadLog();
                $load_logs->user_id = 1;
                $load_logs->quantity = 1;
                $load_logs->type = 'user';
                $load_logs->save();
                $load = new Load();
                $load->control_number = $row[0];
                $load->shipper_id = intval($request->shipper);
                $load->broker_id = session('broker');
                $load->load_log_id = $load_logs->id;
                $load->date = Carbon::parse($row[18]);
                $load->truck_id = $row[1] ? $truck['id']  : null;
                $load->driver_id = $row[2] ? $driver['id'] : null;
                // $load->carrier_id = $driver['carrier_id'] ? $driver['carrier_id'] : null;
                $load->customer_reference = $row[4] ? $row[4]: null;
                $load->bol = $row[5] ? $row[5] : null;
                $load->weight = $row[6] ? $row[6] : null;
                $load->tons  = $row[7] ? $row[7]: null; 
                $load->mileage = $row[8] ?  $row[8] : null; 
                $load->load_type_id =  $loadType['id'];
                $load->trip_id = $trip['id']; 
                $load->customer_po = $row[11]; 
                $load->customer_name = $row[12]; 
                $load->status = $row[13]; 
                $load->destination = $trip['destination']; 
                $load->destination_coords = $trip['destination_coords']; 
                $load->origin = $trip['origin']; 
                $load->origin_coords = $trip['origin_coords']; 
                $load->box_type_id_init = $row[14] ? $row[14] :null; 
                $load->box_type_id_end = $row[15] ? $row[15] :null; 
                $load->save();
                $loadStatus = new LoadStatus();
                $loadStatus->load_id = $load->id;
                $loadStatus->unallocated_timestamp =$row[16] ? $row[16]: null;  
                $loadStatus->requested_timestamp = $row[17] ? $row[17] : null;  
                $loadStatus->accepted_timestamp = $row[18] ? $row[18]: null;  
                $loadStatus->loading_timestamp = $row[19] ? $row[19]: null;  
                $loadStatus->to_location_timestamp = $row[20] ? $row[20]: null; 
                $loadStatus->arrived_timestamp = $row[21]  ? $row[21]: null;  
                $loadStatus->unloading_timestamp = $row[22] ? $row[22]: null; 
                $loadStatus->finished_timestamp =$row[23]  ? $row[23]: null;
                $loadStatus->save();
                // $this->validator($load)->validate();
                // $request->validate([
                //     '$row[0]' => 'required|numeric',

                // ]);
        }

        return ['success' => true, 'load' => $load, 'loadStatus' => $loadStatus, 'loadLogs' =>  $load_logs];
           
    }
}
