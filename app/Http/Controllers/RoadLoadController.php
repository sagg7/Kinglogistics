<?php

namespace App\Http\Controllers;

use App\Enums\LoadTypeEnum;
use App\Models\City;
use App\Models\Load;
use App\Models\LoadMode;
use App\Models\LoadTrailerType;
use App\Models\LoadType;
use App\Models\RoadLoad;
use App\Models\RoadLoadRequest;
use App\Models\Shipper;
use App\Models\State;
use App\Models\DispatchSchedule;
use App\Models\Truck;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoadLoadController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = [
            'trailer_types' => LoadTrailerType::pluck('name', 'id')->toArray(),
            'radius' => [25 => 25, 50 => 50, 100 => 100, 150 => 150, 200 => 200, 300 => 300],
            'load_sizes' => ['full' => 'FULL (Full loads)', 'partial' => 'LTL (Partial loads)'],
            'weight' => [null => 'All', 40000 => 'Less than: 40,000', 30000 => 'Less than: 30,000', 20000 => 'Less than: 20,000', 10000 => 'Less than: 10,000'],
            'length' => [null => 'All', 40 => 'Less than: 40', 30 => 'Less than: 30', 20 => 'Less than: 20', 10 => 'Less than: 10'],
        ];

        return view('loads.road.index', $params);
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'statesOrigin' => ['required'],
            'citiesOrigin' => ['required'],
            'origin_early_pick_up_date' => ['nullable', 'date'],
            'origin_late_pick_up_date' => ['nullable', 'date'],
            'stateDestination' => ['required', 'numeric'],
            'cityDestination' => ['required', 'numeric'],
            'destination_early_pick_up_date' => ['nullable', 'date'],
            'destination_late_pick_up_date' => ['nullable', 'date'],
            'trailer_type_id' => ['required', 'numeric'],
            'mode_id' => ['nullable', 'numeric'],
            'shipper_rate' => ['nullable', 'numeric'],
            'rate' => ['nullable', 'numeric'],
            'weight' => ['nullable', 'numeric'],
            'tons' => ['nullable', 'numeric'],
            'width' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'pieces' => ['nullable', 'numeric'],
            'pallets' => ['nullable', 'numeric'],
            'load_type_id' => ['required', 'numeric'],
            'mileage' => ['nullable', 'numeric'],
            'silo_number' => ['nullable', 'numeric'],
            'customer_po' => ['nullable', 'numeric'],
            'control_number' => ['required', 'string', 'max:255'],
            'pay_rate' => ['nullable', 'numeric'],
            'load_size' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);
    }


    public function storeUpdate(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $date = Carbon::now();
            $coordsOrigin = City::with('state:id,name')->findOrFail($request->citiesOrigin)->first();
            $coordsDestination = City::with('state:id,name')->findOrFail($request->cityDestination)->first();
            $roadLoad = new RoadLoad();
            $load = new Load();
            $load->broker_id = auth()->user()->broker_id;
            $load->shipper_rate = $request->shipper_rate;
            $load->rate = $request->rate;
            $load->weight = $request->weight;
            $load->tons = $request->tons;
            $load->origin = $coordsOrigin->state->name . ', ' . $coordsOrigin->name;
            $load->origin_coords = $coordsOrigin->latitude . ', -' . $coordsOrigin->longitude;
            $load->destination = $coordsOrigin->state->name . ', ' . $coordsOrigin->name;
            $load->destination_coords = $coordsDestination->latitude . ', -' . $coordsDestination->longitude;
            $load->mileage = $request->mileage;
            $load->silo_number = $request->silo_number;
            $load->load_type_id = $request->load_type_id;
            $load->type = 'road';
            $load->customer_po = $request->customer_po;
            $load->control_number = $request->control_number;
            $load->notes = $request->notes;
            $load->shipper_id = auth()->guard('shipper')->check() ? auth()->user()->id : $request->shipper;
            $load->date = Carbon::now()->format('Y-m-d');
            if (auth()->guard('shipper')->check()) {
                $load->creator_type = 'shipper';
            } else if (auth()->guard('web')->check()) {
                $load->creator_type = 'user';
            } else {
                $load->creator_type = 'driver';
            }
            $load->creator_id = auth()->user()->id;
            $dispatch = DispatchSchedule::where('day', $date->dayOfWeek-1)
                ->where('time', $date->format("H").':00:00')->first();
            if ($dispatch)
                $load->dispatch_init = $dispatch->user_id;
            $load->save();

            // TODO: TALK ABOUT THIS IMPLEMENTATION, IT WAS DEFINED AS SET INTO THE SHIPPER PROFILE BEFOREHAND
            /*$shipper = Shipper::findOrFail($load->shipper_id);
            $shipper->days_to_pay = $request->days_to_pay;
            $shipper->save();*/

            $roadLoad->load_id = $load->id;
            $roadLoad->origin_city_id = $request->citiesOrigin;
            $roadLoad->destination_city_id = $request->cityDestination;
            $roadLoad->origin_early_pick_up_date = Carbon::parse($request->origin_early_pick_up_date);
            $roadLoad->origin_late_pick_up_date = Carbon::parse($request->origin_late_pick_up_date);
            $roadLoad->destination_early_pick_up_date = Carbon::parse($request->destination_early_pick_up_date);
            $roadLoad->destination_late_pick_up_date = Carbon::parse($request->destination_late_pick_up_date);
            $roadLoad->trailer_type_id = $request->trailer_type_id;
            $roadLoad->mode_id = $request->mode_id;
            $roadLoad->width = $request->width;
            $roadLoad->height = $request->height;
            $roadLoad->length = $request->length;
            $roadLoad->cube = $request->width * $request->height * $roadLoad->length;
            $roadLoad->pay_rate = $request->pay_rate;
            $roadLoad->load_size = $request->load_size;
            $roadLoad->save();

            return ['success' => true];
        });
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->validator($data)->validate();
        $this->storeUpdate($request);
        if ($request->ajax()) {
            return ['success' => true];
        }
        return $request;
    }

    public function selectionLoadType(Request $request)
    {
        $query = LoadType::select([
            'id',
            'name as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', auth()->user()->broker_id); // check why session('broker') is null
            });

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionTrailerType(Request $request)
    {
        $query = LoadTrailerType::select([
            'id',
            'name as text',
        ]);

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionLoadMode(Request $request)
    {
        $query = LoadMode::select([
            'id',
            'name as text',
        ]);

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionStates(Request $request)
    {
        $query = State::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionCity(Request $request)
    {
        if ($request->statesOrigin) {
            $state = $request->statesOrigin;
        } else {
            $state = $request->stateDestination;
        }
        $query = City::select([
            'id',
            'name as text',
        ])
            ->where('state_id', $state)
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            /*case 'relation':
                $array = [
                    //'relation' => $item,
                    'column' => 'created_at',
                ];
                break;*/
            default:
                $array = null;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        // For more reference about where the distance calculation function was found:
        // @url https://gis.stackexchange.com/questions/31628/find-features-within-given-coordinates-and-distance-using-mysql
        $distanceCalculationSelect = function ($latitude, $longitude) {
            return DB::raw(
                "(" .
                "3959 * acos (" .
                "cos ( radians($latitude) )" .
                "* cos( radians( latitude ) )" .
                "* cos( radians( longitude ) - radians($longitude) )" .
                "+ sin ( radians($latitude) )" .
                "* sin( radians( latitude ) )" .
                ")" .
                ") AS distance"
            );
        };

        if (auth()->guard('carrier')->check()) {
            $requestable_type = 'carrier';
        } else {
            $requestable_type = 'user';
        }

        $query = Load::select([
            'id',
            'date',
            'shipper_id',
            'mileage',
            'weight',
        ])
            ->where('type', LoadTypeEnum::ROAD)
            ->where(function ($q) use ($request) {
                if ($request->weight) {
                    $q->where('weight', '<=', $request->weight);
                }
            })
            ->whereHas('road', function ($q) use ($request, $distanceCalculationSelect) {
                if ($request->load_size) {
                    $q->where('load_size', $request->load_size);
                }
                if ($request->length) {
                    $q->where('length', '<=', $request->length);
                }
                if ($request->trailer_type) {
                    $q->whereIn('trailer_type_id', $request->trailer_type);
                }
                $q->whereHas('origin_city', function ($q) use ($request, $distanceCalculationSelect) {
                    if ($request->origin_city) {
                        $q->whereIn('id', $request->origin_city);
                        foreach ($request->origin_coords as $item) {
                            $q->orWhereHas('locations', function ($q) use ($request, $distanceCalculationSelect, $item) {
                                $q->select('id', 'city_id', $distanceCalculationSelect($item['latitude'], $item['longitude']))
                                    ->having('distance', '<=', $request->origin_radius);
                            });
                        }
                    }
                })
                    ->whereHas('destination_city', function ($q) use ($request, $distanceCalculationSelect) {
                        if ($request->destination_city) {
                            $q->whereIn('id', $request->destination_city);
                            foreach ($request->destination_coords as $item) {
                                $q->orWhereHas('locations', function ($q) use ($request, $distanceCalculationSelect, $item) {
                                    $q->select('id', 'city_id', $distanceCalculationSelect($item['latitude'], $item['longitude']))
                                        ->having('distance', '<=', $request->destination_radius);
                                });
                            }
                        }
                    });

                // This checks if the load has a request that has been accepted, if it has, it doesn't query the load
                $q->whereDoesntHave('request', function ($q) {
                    $q->where('status', 'accepted');
                })
                    ->whereDoesntHave('request'); // Or query the ones that do not have a request at all
            })
            ->with([
                'road' => function ($q) use ($requestable_type) {
                    $q->select([
                        'id',
                        'load_id',
                        'trailer_type_id',
                        'mode_id',
                        'deadhead_miles',
                        'origin_city_id',
                        'destination_city_id',
                        'load_size',
                        'length',
                        'pay_rate',
                        'created_at',
                    ])
                        ->with([
                            'mode:id,name',
                            'trailer_type:id,name',
                            'origin_city' => function ($q) {
                                $q->select(['id', 'state_id', 'name'])
                                    ->with('state:id,abbreviation');
                            },
                            'destination_city' => function ($q) {
                                $q->select(['id', 'state_id', 'name'])
                                    ->with('state:id,abbreviation');
                            },
                            'request' => function ($q) use ($requestable_type) {
                                $filterRequestable = function ($q) {
                                    // If a carrier is searching, filter via the id of the related requestable
                                    if (auth()->guard('carrier')->check()) {
                                        $q->where('id', auth()->user()->id);
                                    } else {
                                        // else if the broker is searching, filter via the requestable broker id
                                        $q->where('broker_id', session('broker'));
                                    }
                                };
                                $q->where('requestable_type', $requestable_type)
                                    ->whereHas('requestable', function ($q) use ($filterRequestable) {
                                        $filterRequestable($q);
                                    })
                                    ->with([
                                        "requestable" => function ($q) use ($filterRequestable) {
                                            $filterRequestable($q);
                                            $q->select('id', 'broker_id');
                                        }
                                    ]);
                            }
                        ]);
                },
                'shipper:id,name,factoring,days_to_pay,phone',
            ])
            ->orderBy('date');

        if ($request->first) {
            $query->take(15);
        }

        $calculateResultsData = function ($query) {
            $now = Carbon::now();
            foreach ($query as $load) {
                $load->age = Carbon::parse($load->road->created_at)->diff($now);
                $start = $load->road->created_at;
                $diffInSeconds = $start->diffInSeconds($now);
                if ($diffInSeconds <= 86400) { // Checks if it's older than 24 hrs
                    $load->age = $start->diff($now)->format('%hh%im');
                } else {
                    $load->age = Carbon::parse($start)->diffInDays($now) . 'd' . $start->diff($now)->format('%hh%im');
                }
                $load->rate_mile = ($load->road->pay_rate && $load->mileage) ? ($load->road->pay_rate / $load->mileage) : 0;
            }
        };

        if ($request->dispatch) {
            $array = $request->searchable;
            $query->where(function ($q) use (&$array, $request) {
                $statement = 'whereHas';
                foreach ($array as $idx => $item) {
                    switch ($item) {
                        case 'age':
                            $array[$idx] = 'created_at';
                            break;
                        case 'origin_city':
                        case 'destination_city':
                        case 'trailer_type':
                            $q->$statement('road', function ($q) use ($item, $request) {
                                $q->whereHas($item, function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%$request->search%");
                                });
                            });
                            $statement = 'orWhereHas';
                            unset($array[$idx]);
                            break;
                        case 'origin_state':
                        case 'destination_state':
                            $q->orWhereHas('road', function ($q) use ($item, $request) {
                                if ($item === 'origin_state') {
                                    $relation = 'origin_city';
                                } else {
                                    $relation = 'destination_city';
                                }
                                $q->whereHas($relation, function ($q) use ($request) {
                                    $q->whereHas('state', function ($q) use ($request) {
                                        $q->where('name', 'LIKE', "%$request->search%")
                                            ->orWhere('abbreviation', 'LIKE', "%$request->search%");
                                    });
                                });
                            });
                            unset($array[$idx]);
                            break;
                        case 'load_size':
                        case 'length':
                        case 'pay_rate':
                            $q->orWhereHas('road', function ($q) use ($item, $request) {
                                $q->where($item, 'LIKE', "%$request->search%");
                            });
                            unset($array[$idx]);
                            break;
                        case 'shipper':
                            $q->orWhereHas($item, function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%$request->search%");
                            });
                            unset($array[$idx]);
                            break;
                        default:
                            break;
                    }
                }
            });
            $request->searchable = $array;
            $result = $this->multiTabSearchData($query, $request, null, 'orWhere');
            $calculateResultsData($result['rows']);
            return $result;
        } else {
            $query = $query->whereDate('date', '>=', Carbon::parse($request->ship_date_start))
                ->whereDate('date', '<=', Carbon::parse($request->ship_date_end))
                ->get();
            $calculateResultsData($query);
        }

        return $query;
    }

    public function request(Request $request)
    {
        if (auth()->guard('web')->check()) {
            $carrier = Truck::with('carrier')->find($request->truck_id)->carrier;
            $requestable_type = 'user';
        } else if (auth()->guard('carrier')->check()) {
            $carrier = auth()->user();
            $requestable_type = 'carrier';
        } else {
            return abort(404);
        }
        $loadRequest = new RoadLoadRequest();
        $loadRequest->road_load_id = $request->road_load_id;
        $loadRequest->carrier_id = $carrier->id;
        $loadRequest->truck_id = $request->truck_id;
        $loadRequest->requestable_id = auth()->user()->id;
        $loadRequest->requestable_type = $requestable_type;

        return ['success' => $loadRequest->save()];
    }

    public function indexDispatch()
    {
        return view('loads.road.dispatch.index');
    }

    public function getRequests(Request $request) {
        return RoadLoadRequest::where('road_load_id', $request->road_load_id)
            ->with([
                'carrier:id,name',
                'truck:id,number',
            ])
            ->get();
    }

    public function acceptRequest(Request $request) {
        return DB::transaction(function () use ($request) {
            $loadRequest = RoadLoadRequest::with([
                'road.parentLoad',
            ])
                ->findOrFail($request->request_id);

            RoadLoadRequest::where('road_load_id', $request->road_load_id)
                ->where('id', '!=', $request->request_id)
                ->update(['status' => 'rejected']);

            $loadRequest->status = 'accepted';
            $loadRequest->save();

            $loadRequest->road->parentLoad->truck_id = $loadRequest->truck_id;
            $loadRequest->road->parentLoad->save();

            return ['success' => true];
        });
    }
}
