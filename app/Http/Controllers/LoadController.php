<?php

namespace App\Http\Controllers;

use App\Events\LoadUpdate;
use App\Exports\LoadsExport;
use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Load;
use App\Models\LoadLog;
use App\Models\LoadStatus;
use App\Models\Shipper;
use App\Models\Trip;
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
use Maatwebsite\Excel\Excel;

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
            $control_number = (int)$request->control_number;
            for ($i = 0; $i < $request->load_number; $i++) {
                if (isset($request->driver_id)){ //temporary
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
                $data['control_number'] = $control_number;

                $data['broker_id'] = session('broker');
                $load = $this->storeUpdate($data);

                $control_number++;

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
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth()->subMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

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
                    ])
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
            if($request->type == 'active'){
                $query->where('loads.status', '!=','finished');
                if (empty($request->sortModel))
                    $query->orderBy('accepted_timestamp', 'desc');
            }
            else {
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
                ->whereBetween( DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end]);
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
            $request->search = $date[2].'-'.$date[0].'-'.$date[1];
        }
        $query->select($select);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    public function replacePhoto(Request $request, $id, $type){

        $load_status = LoadStatus::whereHas('parentLoad', function ($q) {
            $q->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        })
            ->where('load_id', $id)->first();

        $new_voucher = $this->uploadImage($request[json_decode($request->slim[0])->output->field], "loads/$load_status->id",30);
        if ($type == "to_location") {
            $load_status->to_location_voucher = $new_voucher;
        } else {
            $load_status->finished_voucher = $new_voucher;
        }

        if ($load_status->save()){
            return [
                        "status"=>"false",
                        "name"=>"uid_filename.jpg",
                        "path"=>"path/uid_filename.jpg"
                    ];
        } else {
            return [
                "status"=>"success",
                "name"=>"uid_filename.jpg",
                "path"=>"path/uid_filename.jpg"
            ];
        }

    }

    function deleteDirectory($dir) {
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

    public function loadPhoto($id, $type){
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
            Storage::put("public/loads/".$load_status->load_id."/to_location.jpg", $contents);
            return asset("storage/loads/".$load_status->load_id."/to_location.jpg");
        } else {
            $url = $this->getTemporaryFile("$load_status->finished_voucher");
            $contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            Storage::put("public/loads/".$load_status->load_id."/finished.jpg", $contents);
            return asset("storage/loads/".$load_status->load_id."/finished.jpg");
        }
    }

    public function DownloadExcelReport(Request $request) {
       // $request->endRow = 0;
       // $request->startRow = 1000;
        $dates = explode(" - ", $request->dateRange);
        $startA = explode("/",$dates[0]);
        $endA = explode("/",$dates[1]);

        $request->merge(["start"=>$startA[2]."-".$startA[0]."-".$startA[1]]);
        $request->merge(["end"=>$endA[2]."-".$endA[0]."-".$endA[1]]);
        $request->merge(["endRow"=>1000]);
        $request->merge(["startRow"=>0]);
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
                    $q->whereBetween( DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end]);
                    if (!empty($request->shipper)) //quitar cuando todos tengan trailer
                        $q->where("shipper_id", "$request->shipper");
            })
            ->orderBy("name");

        return (new LoadsExport($result->get()))->download('Dispatch Report.xlsx');


    }

    public function pictureReport(Request $request){
        $dates = explode(" - ", $request->dateRange);
        $startA = explode("/",$dates[0]);
        $endA = explode("/",$dates[1]);


        $start = $startA ? Carbon::parse($startA[2]."-".$startA[0]."-".$startA[1]) : Carbon::now()->startOfMonth();
        $end = $endA ? Carbon::parse($endA[2]."-".$endA[0]."-".$endA[1])->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

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
            ->whereBetween( DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$start, $end])
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
        foreach ($query->select($select)->get() as $key => $load){
            $loads[] = [
                'driverName'=> $load->driver->name,
                'job'=> $load->trip->name,
                'control_number'=> $load->control_number,
                'customer_reference'=> $load->customer_reference,
                'bol'=>$load->bol,
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

    public function getLoadNote($id){
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

    public function finishLoad($id){
        $load = Load::find($id);
        $loadStatus = LoadStatus::where('load_id', $id)->first();

        switch ($load->status){
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
}
