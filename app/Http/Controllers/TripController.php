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
            'origin_id' => ['required', 'exists:origins,id'],
            'destination_id' => ['required', 'exists:destinations,id'],
            'mileage' => ['required', 'numeric'],
        ], [], [
            'shipper_id' => 'customer',
            'zone_id' => 'zone',
            'rate_id' => 'rate',
            'origin_id' => 'origin',
            'destination_id' => 'destination',
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
            $trip->origin_id = $request->origin_id;
            $trip->destination_id = $request->destination_id;
            $trip->name = $request->name;
            $trip->customer_name = $request->customer_name;
            $trip->mileage = $request->mileage;
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
            'trip_origin:id,name',
            'trip_destination:id,name',
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
     * @return array
     */
    public function destroy(int $id): array
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

        return ['success' => $trip->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Trip::select([
            'id',
            'name AS text',
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
            ->with([
                'trip_origin',
                'trip_destination',
            ])
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
                'trip_destination:id,status_current,status_total',
            ])
            ->get(['id', 'name', 'mileage', 'destination_id', 'status', 'status_current', 'status_total']);

        foreach ($trips as $trip) {
            if ($trip->trip_destination) {
                $current_status = $trip->trip_destination->status_current;
                $status_total = $trip->trip_destination->status_total;
                $trip->status_current = $current_status;
                $trip->status_total = $status_total;
            } else {
                $current_status = $trip->status_current;
                $status_total = $trip->status_total;
            }
            $trip->percentage = $current_status ? ($current_status * 100) / $status_total : 0;
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
