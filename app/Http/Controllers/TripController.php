<?php

namespace App\Http\Controllers;

use App\Enums\CarrierPaymentEnum;
use App\Enums\ShipperInvoiceEnum;
use App\Models\Trip;
use App\Models\Turn;
use App\Models\Zone;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\RecalculateTotals;
use App\Traits\Turn\DriverTurn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, RecalculateTotals, DriverTurn;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'zones' => [null => ''] + Zone::whereHas('broker', function ($q) {
                    $q->where('id', session('broker') ?? auth()->user()->broker_id);
                })
                    ->pluck('name', 'id')->toArray(),
            'statuses' => [null => '', 'stage' => 'Stage', 'loads' => 'Loads'],
        ];
    }

    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'zone_id' => ['required', 'exists:zones,id'],
            'shipper_id' => ['required', 'exists:shippers,id'],
            'rate_id' => ['required', 'exists:rates,id'],
            'origin' => ['required', 'string', 'max:255'],
            'origin_coords' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'destination_coords' => ['required', 'string', 'max:255'],
            'mileage' => ['required', 'numeric'],
            'status' => ['required'],
            'status_current' => ['numeric'],
            'status_total' => ['numeric'],
        ], [
            'origin_coords.required' => 'The origin map location is required',
            'destination_coords.required' => 'The destination map location is required',
        ], [
            'shipper_id' => 'shipper',
            'zone_id' => 'zone',
            'rate_id' => 'rate',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('trips.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('trips.create', $params);
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

        return redirect()->route('trip.index');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Trip
     */
    private function storeUpdate(Request $request, $id = null): Trip
    {
        return DB::transaction(function () use ($request, $id) {
            if ($id) {
                $trip = Trip::where(function ($q) {
                    if (auth()->guard('web')->check()) {
                        $q->whereHas('broker', function ($q) {
                            $q->where('id', session('broker'));
                        });
                    }
                    if (auth()->guard('shipper')->check()) {
                        $q->where('shipper_id', auth()->user()->id);
                    }
                })
                    ->findOrFail($id);
                if ($trip->rate_id != $request->rate_id) {
                    $this->byRateChange($trip, $request->rate_id);
                }
            } else {
                $trip = new Trip();
                $trip->broker_id = session('broker') ?? auth()->user()->broker_id;
            }

            $shipper = auth()->guard('shipper')->check() ? auth()->guard()->user()->id : $request->shipper_id;

            $trip->zone_id = $request->zone_id;
            $trip->shipper_id = $shipper;
            $trip->rate_id = $request->rate_id;
            $trip->name = $request->name;
            $trip->customer_name = $request->customer_name;
            $trip->origin = $request->origin;
            $trip->origin_coords = $request->origin_coords;
            $trip->destination = $request->destination;
            $trip->destination_coords = $request->destination_coords;
            $trip->mileage = $request->mileage;
            $trip->status = $request->status;
            $trip->status_current = $request->status_current;
            $trip->status_total = $request->status_total;
            $trip->save();

            return $trip;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function show(Trip $trip)
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
        $trip = Trip::with([
            'zone:id,name',
            'shipper:id,name',
            'rate' => function ($q) {
                $q->with('rate_group:id,name')
                    ->select([
                        'id',
                        'zone_id',
                        'rate_group_id',
                        'start_mileage',
                        'end_mileage',
                    ]);
            }
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
            })
            ->findOrFail($id);
        $params = compact('trip') + $this->createEditParams();
        return view('trips.edit', $params);
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
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('trip.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $trip = Trip::where(function ($q) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            }
            if (auth()->guard('shipper')->check())
                $q->where('shipper_id', auth()->user()->id);
        })
            ->findOrFail($id);

        if ($trip) {
            /*$message = '';
            if ($trip->loads()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Trip', ['constraint' => 'loads']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else*/
            return ['success' => $trip->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Trip::select([
            'id',
            DB::raw("CONCAT(name, ': ', origin, ' - ', destination) as text"),
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
                else if ($request->shipper)
                    $q->where('shipper_id', $request->shipper);
            });

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function getTrip(Request $request)
    {
        return Trip::where(function ($q) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            }
            if (auth()->guard('shipper')->check())
                $q->where('shipper_id', auth()->user()->id);
        })
            ->findOrFail($request->id);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'zone':
            case 'shipper':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Trip::select([
            "trips.id",
            "trips.name",
            "trips.zone_id",
            "trips.shipper_id",
            "trips.origin",
            "trips.destination",
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
            })
            ->with([
                'zone:id,name',
                'shipper:id,name',
            ]);

        switch ($type) {
            case 'inactive':
                $query = $query->onlyTrashed();
                break;
            case 'active':
            default:
                break;
        }

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    private function loadFilter($q, $turn)
    {
        $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
            ->whereNotNull('unallocated_timestamp');
        if ($turn->start->isBefore($turn->end)) {
            $q->whereDate('unallocated_timestamp', '>=', $turn->start)
                ->whereDate('unallocated_timestamp', '<=', $turn->end);
        } else {
            $q->whereDate('unallocated_timestamp', '<=', $turn->start)
                ->whereDate('unallocated_timestamp', '>=', $turn->end->subDay());
        }
    }

    public function dashboardData()
    {
        $turn = Turn::select('*');
        $this->filterByActiveTurn($turn);
        $turn = $turn->first();
        $trips = Trip::where(function ($q) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            }
            if (auth()->guard('shipper')->check())
                $q->where('shipper_id', auth()->user()->id);
        })
            ->whereHas('loads', function ($q) use ($turn) {
                $this->loadFilter($q, $turn);
            })
            ->with([
                'loads' => function ($q) use ($turn) {
                    $this->loadFilter($q, $turn);
                    $q->select(['loads.id', 'trip_id', 'accepted_timestamp', 'finished_timestamp', 'unallocated_timestamp']);
                },
            ])
            ->get();

        foreach ($trips as $trip) {
            $trip->percentage = $trip->status_current ? ($trip->status_current * 100) / $trip->status_total : 0;
            $avgMinutesSum = 0;
            $avgMinutesCount = 0;
            $loadTimeSum = 0;
            $loadTimeCount = 0;
            foreach ($trip->loads as $idx => $load) {
                if (isset($trip->loads[$idx - 1])) {
                    $avgMinutesSum += Carbon::parse($load->loadStatus->unallocated_timestamp)->diffInMinutes($trip->loads[$idx - 1]->loadStatus->unallocated_timestamp);
                    $avgMinutesCount++;
                }
                if ($load->loadStatus->finished_timestamp) {
                    $loadTimeSum += Carbon::parse($load->loadStatus->accepted_timestamp)->diffInMinutes($load->loadStatus->finished_timestamp);
                    $loadTimeCount++;
                }
            }
            // Calculates avg creation time in minutes between load and load on the same trip
            $trip->avg = $avgMinutesCount > 0 ? (double)number_format($avgMinutesSum / $avgMinutesCount, 2) : 0;
            // Calculates avg time in minutes between the time the load started and it when it was finished
            $trip->load_time = $loadTimeCount > 0 ? (double)number_format($loadTimeSum / $loadTimeCount, 2) : 0;
        }

        return $trips->toArray();
    }
}
