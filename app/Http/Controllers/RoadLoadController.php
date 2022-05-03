<?php

namespace App\Http\Controllers;

use App\Enums\LoadTypeEnum;
use App\Models\City;
use App\Models\Load;
use App\Models\LoadMode;
use App\Models\LoadTrailerType;
use App\Models\LoadType;
use App\Models\RoadLoad;
use App\Models\Shipper;
use App\Models\State;
use App\Models\DispatchSchedule;
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
        $load->shipper_id = auth()->user()->id;
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

        $shipper = Shipper::findOrFail($load->shipper_id);
        $shipper->days_to_pay = $request->days_to_pay;
        $shipper->save();


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

        $query = Load::select([
            'id',
            'date',
            'shipper_id',
            'mileage',
            'weight',
        ])
            ->where('type', LoadTypeEnum::ROAD)
            ->whereDate('date', '>=', Carbon::parse($request->ship_date_start))
            ->whereDate('date', '<=', Carbon::parse($request->ship_date_end))
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
            })
            ->with([
                'road' => function ($q) {
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
                        ]);
                },
                'shipper:id,name,factoring,days_to_pay',
            ])
            ->orderBy('date');

        if ($request->first) {
            $query->take(15);
        }

        $query = $query->get();

        $now = Carbon::now();
        foreach ($query as $load) {
            $load->age = Carbon::parse($load->road->created_at)->diff($now);
            $start = $load->road->created_at;
            $diffInSeconds = $start->diffInSeconds($now);
            if ($diffInSeconds <= 86400) { // Checks if it's older than 24 hrs
                $load->age = $start->diff($now)->format('%hh%im');
            } else {
                $load->age = $start->diffInHours($now) . 'd' . $start->diff($now)->format('%hh%im');
            }
            $load->rate_mile = ($load->road->pay_rate && $load->mileage) ? ($load->road->pay_rate / $load->mileage) : 0;
        }

        return $query;
    }

    public function indexDispatch()
    {
        return view('loads.road.dispatch.index');
    }
}
