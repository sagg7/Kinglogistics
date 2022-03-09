<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\dispatch_report;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Load;
use App\Models\LoadStatus;
use App\Models\Shipper;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailyLoads(Request $request)
    {
        return view('reports.dailyLoads');
    }

    public function createDailyDispatchReport(){
        $name = auth()->user()->name;
        $userId = auth()->user()->id;
        $now = Carbon::now('America/Chicago');


        //avg
        $start = Carbon::now()->subHour(12);
        $end = Carbon::now();
        $loadsAccepted = Load::join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
        ->whereBetween('accepted_timestamp', [$start, $end]);
        $loads = $loadsAccepted->get();
        $shipperAvg = [];
        $totalTime = 0;
        $date = null;
        $count = 0;
        foreach ($loads as $key => $load) {
            if ($date != null) {
                $totalTime += Carbon::parse($load->accepted_timestamp)->diffInMinutes($date);
            }
            $date = $load->accepted_timestamp;
            $count++;
        }

        // Time of session vs schedule
        $dateIn = DB::table('sessions')
        ->select('last_activity')
        ->where('user_id', $userId)
        ->first()->last_activity;

        //worked_time: calcular date in vs now();
        $dateIn = date('Y-m-d H:i:s',$dateIn);

        $worked_time = Carbon::parse($dateIn)->diffInMinutes($now);
        $max_load = dispatch_report::max('loads_finalized');
        $loads_finalized = Load::where('dispatch_id',$userId)
        ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
        ->whereBetween('load_statuses.finished_timestamp', [$now->subHours(12), $now])
         ->where('broker_id', session('broker'))->count();

         //total loads
         $total_loads =0;
         if($loads_finalized!=0)
            $total_loads =$loads_finalized*100/$max_load;

        $active_loads = Load::where('status','!=','finished')
        ->where('broker_id', session('broker'))->count();

        //this is just an if to know if we have 0
        if($active_loads != 0 && $loads_finalized != 0){
            $loads_pending_to_shift = $active_loads*100/$loads_finalized;
        }else $loads_pending_to_shift = 0;

        $load_time_avg =round(($count !== 0) ? $totalTime/$count : 0);
        $active_drivers=Driver::where('status','!=','inactive')
                            ->where('broker_id', session('broker'))->whereHas('truck')->count();

        //convert each variable to percentage to get 100% of dispatch_score
        $load_time_avg_score = $load_time_avg*100/240*.05; //240 4hrs per load TODO temporary until we get real AVG per load
        $worked_time_score = $worked_time*100/480*.05; //480 8hrs TODO temporary until we get the schedule
        $total_loads_score = $total_loads*.3;
        $score_app_usage_score = $loads_pending_to_shift*30/100;
        $active_drivers_score = $active_drivers*30/100;

        $params = [
            'name'=> $name,
            'date'=> $now->format('d-m-Y'),
            'hours' => $now->toTimeString(),
            'active_loads' => $active_loads,
            // Metric to get dispatch_score "Active drivers or truck active = 30%"
            'active_drivers' => $active_drivers,
            'inactive_drivers' => Driver::where('status','=','inactive')
                            ->whereNull('inactive')
                            ->where('broker_id', session('broker'))->whereHas('truck')->count(),
            'loads_finalized' => $loads_finalized,
            //Metric to get loads to pending at the time end shift 30%
            'loads_pending_to_shift'=> $loads_pending_to_shift,
            // Metric to get dispatch_score "Total loads vs max load = 30%"
            'total_loads'=> $total_loads,
            // Metric to get dispatch_score "Avg to receive load = 5%"
            'Load_time_avg' => $load_time_avg,
            //"session time vs shedule = 5%"
            'worked_time' => $worked_time,
            "dispatch_score" => round($load_time_avg_score + $worked_time_score +  $total_loads_score + $score_app_usage_score + $active_drivers_score, 2),
            'score_app_usage'=> $loads_pending_to_shift,

        ];
        return $params;
    }


    public function storeDispatchReport(Request $request){
        $dispatch_report = new dispatch_report();
        $name = auth()->user()->name;
        $userId = auth()->user()->id;
        $now = Carbon::now('America/Chicago');


        //avg
        $start = Carbon::now()->subHour(12);
        $end = Carbon::now();
        $loadsAccepted = Load::join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
        ->whereBetween('accepted_timestamp', [$start, $end]);
        $loads = $loadsAccepted->get();
        $shipperAvg = [];
        $totalTime = 0;
        $date = null;
        $count = 0;
        foreach ($loads as $key => $load) {
            if ($date != null) {
                $totalTime += Carbon::parse($load->accepted_timestamp)->diffInMinutes($date);
            }
            $date = $load->accepted_timestamp;
            $count++;
        }

        // Time of session vs schedule
        $dateIn = DB::table('sessions')
        ->select('last_activity')
        ->where('user_id', $userId)
        ->first()->last_activity;

        //worked_time: calcular date in vs now();
        $dateIn = date('Y-m-d H:i:s',$dateIn);

        $worked_time = Carbon::parse($dateIn)->diffInMinutes($now);
        $max_load = dispatch_report::max('loads_finalized');
        $loads_finalized = Load::where('dispatch_id',$userId)
        ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
        ->whereBetween('load_statuses.finished_timestamp', [$now->subHours(12), $now])
         ->where('broker_id', session('broker'))->count();

         //total loads
         $total_loads =0;
         if($loads_finalized!=0)
            $total_loads =$loads_finalized*100/$max_load;

        $active_loads = Load::where('status','!=','finished')
        ->where('broker_id', session('broker'))->count();

        //this is just an if to know if we have 0
        if($active_loads != 0 && $loads_finalized != 0){
            $loads_pending_to_shift = $active_loads*100/$loads_finalized;
        }else $loads_pending_to_shift = 0;

        $load_time_avg =round(($count !== 0) ? $totalTime/$count : 0);
        $active_drivers=Driver::where('status','!=','inactive')
                            ->where('broker_id', session('broker'))->whereHas('truck')->count();

        //convert each variable to percentage to get 100% of dispatch_score
        $load_time_avg_score = $load_time_avg*100/240*.05; //240 4hrs per load TODO temporary until we get real AVG per load
        $worked_time_score = $worked_time*100/480*.05; //480 8hrs TODO temporary until we get the schedule
        $total_loads_score = $total_loads*.3;
        $score_app_usage_score = $loads_pending_to_shift*30/100;
        $active_drivers_score = $active_drivers*30/100;


        $dispatch_report->dispatch_id = $userId;
        $dispatch_report->date = $now;
        $dispatch_report->active_loads = $active_loads;
        $dispatch_report->active_drivers = $active_drivers;
        $dispatch_report->inactive_drivers = Driver::where('status','=','inactive')
        ->where('broker_id', session('broker'))->count();
        $dispatch_report->well_status = $request->wellStatus;
        $dispatch_report->loads_finalized = $loads_finalized;
        $dispatch_report->worked_time = $worked_time;
        $dispatch_report->dispatch_score = ($load_time_avg_score + $worked_time_score +  $total_loads_score + $score_app_usage_score + $active_drivers_score);
        $dispatch_report->score_app_usage = $loads_pending_to_shift;
        $dispatch_report->description = $request->situationDescription;
        $dispatch_report->save();

        return ['success' => true, 'data' => $dispatch_report];
    }

    public function getDispatchReport(){
        $report = dispatch_report::with('dispatch')
                    ->whereHas('dispatch', function($q){
                        $q->where('broker_id', session('broker'));
                    })
                    ->orderBy('date', 'desc')->take(10)->get();
        return $report;
    }

    public function showDispatchReportById($id,LoadStatus $Load_statuse ){
        $report = dispatch_report::find($id);
        $Formated =Carbon::createFromFormat('Y-m-d H:i:s', $report->date);
        $dateFormated = $Formated->format('m-d-Y');
        $hoursFormated = $Formated->format('H:i');

        $params = [
            'name'=> $report->dispatch->name,
            'date'=> $dateFormated,
            'hours' => $hoursFormated,
            'active_loads' => $report->active_loads,
            'active_drivers' => $report->active_drivers,
            'inactive_drivers' => $report->inactive_drivers,
            'loads_finalized' => $report->loads_finalized,
            'worked_time' => $report->worked_time,
            "dispatch_score" => $report->dispatch_score,
            'score_app_usage'=> $report->score_app_usage,
            'well_status'=> $report->well_status,
            'description'=> $report->description
        ];
        // dd($hoursFormated);
        return $params;
    }


    private function compareDates($a, $b)
    {
        //$a = Carbon::parse($a);
        return $a->isAfter($b);
    }

    public function dailyLoadsData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        $count2 = [];

        // TODO: FORMAT DATA TO SHOW FOR PERIOD FILTER, RIGHT NOW THE DATES ARE KINDA WEIRD ON THE GRAPH AND GOTTA SHOW NEW READABLE FORMATS
        switch ($request->period) {
            default:
            case 'day':
                $date_group = DB::raw("DATE(finished_timestamp) AS date_group");
                $date_readable_format = "M d";
                $goal = 250;
                break;
            case 'week':
                $date_group = DB::raw("CONCAT(YEAR(finished_timestamp), '/', WEEK(finished_timestamp, 1)) AS date_group");
                $date_readable_format = "M d";
                $goal = 250 * 7;
                break;
            case 'month':
                $date_group = DB::raw("CONCAT(YEAR(finished_timestamp), '/', MONTH(finished_timestamp)) AS date_group");
                $date_readable_format = "M";
                $goal = 250 * $end->day;
                break;
        }

        $filterLoads = function ($q) use ($request, $start, $end, $date_group) {
            $select = [
                'loads.id', 'trip_id', 'shipper_id', 'finished_timestamp',
                DB::raw('COUNT(*) as loads_count'),
                $date_group,
            ];
            $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                ->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                ->whereDate('finished_timestamp', '>=', $start)
                ->whereDate('finished_timestamp', '<=', $end)
                ->orderBy('finished_timestamp')
                ->groupBy('date_group')
                ->select($select);

            switch ($request->graph_type) {
                default:
                case 'trips':
                    $q->groupBy('trip_id');
                    break;
                case 'shippers':
                    $q->groupBy('shipper_id');
                    break;
                case 'total':
                    break;
            }
        };

        $checkOnLoadsRelation = function ($q) use ($start, $end) {
            $q->whereHas('loads', function ($q) use ($start, $end) {
                $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                    ->whereDate('finished_timestamp', '>=', $start)
                    ->whereDate('finished_timestamp', '<=', $end);
            });
        };

        $dates = [];
        $series = [];
        $storeData = function ($load, &$count) use (&$dates) {
            // Set the date formatted for the array position and dates array
            $formatted = Carbon::parse($load->finished_timestamp)->format('Y-m-d');
            // If this date hasn't been previously pushed to the dates array, push it
            if (!in_array($formatted, $dates))
                $dates[] = $formatted;
            // Set the loads count amount on the formatted date position
            $count[$formatted] = $load->loads_count;
        };
        switch ($request->graph_type) {
            default:
            case "trips": // Format the data per trip
                $query = Trip::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->with([
                        'loads' => function ($q) use ($filterLoads) {
                            $filterLoads($q);
                        },
                        'shipper:id,name',
                    ])
                    ->withTrashed()
                    ->where(function ($q) use ($request, $checkOnLoadsRelation) {
                        if ($request->shipper)
                            $q->where('shipper_id', $request->shipper);
                        $checkOnLoadsRelation($q);
                    })
                    ->get(['id', 'name', 'shipper_id']);
                foreach ($query as $trip) {
                    $count = [];
                    foreach ($trip->loads as $load) {
                        $storeData($load, $count);
                    }
                    $series[] = [
                        'name' => $trip->name,
                        'data' => $count,
                    ];
                }
                break;
            case "shippers":
                $query = Shipper::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->with(['loads' => function ($q) use ($filterLoads) {
                        $filterLoads($q);
                    }])
                    ->where(function ($q) use ($request, $checkOnLoadsRelation) {
                        if ($request->shipper)
                            $q->where('id', $request->shipper);
                        $checkOnLoadsRelation($q);
                    })
                    ->get(['id', 'name']);
                foreach ($query as $shipper) {
                    $count = [];
                    foreach ($shipper->loads as $load) {
                        $storeData($load, $count);
                    }
                    $series[] = [
                        'name' => $shipper->name,
                        'data' => $count,
                    ];
                }
                break;
            case "total":
                $query = Load::where(function ($q) use ($request) {
                    if ($request->shipper)
                        $q->where('shipper_id', $request->shipper);
                });
                $filterLoads($query);
                $query = $query->get();
                $count = [];

                foreach ($query as $load) {
                    $storeData($load, $count);
                    $count2[] = $goal;
                }

                $series[] = [
                    'data' => $count,
                    'name' => 'Loads',
                ];
                break;
        }

        // Sort dates to correct order
        array_multisort($dates);
        // Loop trough each trip data
        foreach ($series as $key => $item) {
            $data = [];
            // Using the saved dates array, saved the count of each date
            foreach ($dates as $date) {
                if (array_key_exists($date, $item['data'])) {
                    // Push the previous count data in the correct position according to the dates array
                    $data[] = $item['data'][$date];
                } else { // If there was no previous count for the date, push it as 0
                    $data[] = 0;
                }
            }
            // Replaced the previous count to the new one, including dates as count 0
            $series[$key]['data'] = $data;
        }

        // Finally, format the dates array to a human-readable format
        foreach ($dates as $key => $date) {
            $dates[$key] = Carbon::parse($date)->format($date_readable_format);
        }

        $average = [];
        $total = null;
        foreach ($data as $key => $item) {
            if ($key == 0) {
                $average[] = $item;
                $total = $item;
            } elseif ($key > 30) {
                $total += $item - $data [$key - 30];
                $average[] = Round($total / 30);
            } else {
                $total += $item;
                $average[] = Round($total / ($key + 1));
            }
        }

		 if (isset($average) && ($request->graph_type == 'total'))
            $series[] = [
                'data' => $average,
                'name' => 'Average',
            ];

        if (isset($count2) && ($request->graph_type == 'total'))
            $series[] = [
                'data' => $count2,
                'name' => 'Goal',
            ];


        return ['series' => $series, 'categories' => $dates];
    }


    public function activeTime()
    {
        return view('reports.activeTime');
    }

    public function activeTimeData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfWeek();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();

        $driversWorkedHoursFilter = function ($q) use ($start, $end) {
            $q->whereBetween('shift_start', [$start, $end])
                ->orWhereBetween('shift_end', [$start, $end])
                ->orWhereNull('shift_end');
        };
        $now = Carbon::now();
        $query = Carrier::whereHas('drivers', function ($q) use ($driversWorkedHoursFilter) {
            $q->whereHas('worked_hours', function ($q) use ($driversWorkedHoursFilter) {
                $driversWorkedHoursFilter($q);
            });
        })
            ->with([
                'drivers' => function ($q) use($start, $end, $driversWorkedHoursFilter) {
                    $q->whereHas('worked_hours', function ($q) use ($driversWorkedHoursFilter) {
                        $driversWorkedHoursFilter($q);
                    })
                        ->with([
                            'truck:id,number',
                            'worked_hours' => function ($q) use ($driversWorkedHoursFilter) {
                                $driversWorkedHoursFilter($q);
                            },
                            'loads' => function ($q) use ($start, $end) {
                                $q->join('load_statuses', 'load_statuses.load_id', 'loads.id')
                                    ->whereBetween('accepted_timestamp', [$start, $end])
                                    ->orWhereBetween('finished_timestamp', [$start, $end])
                                    ->orWhereNull('finished_timestamp');
                            }
                        ]);
                }
            ])
            ->get();

        $rangeEnd = $end->isAfter($now) ? $now : $end;
        $dateRangeHoursTotal = $start->diffInMinutes($rangeEnd) / 60;
        $driversData = [];
        $carriersData = [];
        foreach ($query as $idx => $carrier) {
            $carrierWorkedHours = 0;
            $carrierTrucks = [];
            foreach ($carrier->drivers as $driver) {
                $driver_worked_hours = 0;
                // Calculate the worked time data
                foreach ($driver->worked_hours as $item) {
                    $shift_start = Carbon::parse($item->shift_start);
                    $isBeforeStartFilter = $shift_start->isBefore($start);
                    if ($isBeforeStartFilter || !$item->shift_end) {
                        // If there's no shift_end timestamp, then use Carbon::now()
                        $shift_end = $item->shift_end ? Carbon::parse($item->shift_end) : $now;
                        // If the shift started before the filter $start date, use the $start value instead
                        if ($isBeforeStartFilter) {
                            $shift_start = $start;
                        }
                        // Sum the worked hours
                        $driver_worked_hours += (Carbon::parse($shift_start)->diffInMinutes($shift_end)) / 60;
                    } else {
                        // Sum the db calculated worked hours
                        $driver_worked_hours += $item->worked_hours;
                    }
                }
                $driver_loaded_time = 0; // Represents the sum of all the time the driver had a load (from accepted_timestamp to finished_timestamp)
                foreach ($driver->loads as $load) {
                    // If the load has a truck_id, and it hasn't been stored on the carrierTrucks array
                    if ($load->truck_id && !in_array($load->truck_id, $carrierTrucks, false)) {
                        $carrierTrucks[] = $load->truck_id;
                    }
                    // Calculate active time on load
                    $accepted_timestamp = Carbon::parse($load->accepted_timestamp);
                    $finished_timestamp = $load->finished_timestamp ? Carbon::parse($load->finished_timestamp) : $now;
                    // If the accepted timestamp is before the filter $start date, use $start value instead
                    if ($accepted_timestamp->isBefore($start)) {
                        $accepted_timestamp = $start;
                    }
                    $driver_loaded_time += ($accepted_timestamp->diffInMinutes($finished_timestamp)) / 60;
                }
                $driversData[] = [
                    'id' => $driver->id,
                    'truck' => $driver->truck,
                    'carrier_id' => $driver->carrier_id,
                    'name' => $driver->name,
                    'active_time' => $driver_worked_hours,
                    'inactive_time' => $dateRangeHoursTotal - $driver_worked_hours, // The inactive time is calculated via the total of hours on the week minus the driver worked hours sum
                    'loaded_time' => $driver_loaded_time,
                    'waiting_time' => $driver_worked_hours - $driver_loaded_time, // The total active time of the driver minus the time it had a load assigned
                ];
                $carrierWorkedHours += $driver_worked_hours;
            }
            $carriersData[] = [
                'id' => $carrier->id,
                'name' => $carrier->name,
                'active_time' => $carrierWorkedHours,
                'trucks' => count($carrierTrucks), // Gets the quantity of unique trucks used on the loads
            ];
        }

        return compact('driversData', 'carriersData');
    }

    public function utilityProjection()
    {
        return view('reports.utilityProjection');
    }

    public function utilityProjectionData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfWeek();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();

        $loads = Load::join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
            ->with([
                'driver' => function ($q) {
                    $q->select('id', 'carrier_id')
                        ->with('carrier:id,name');
                }
            ])
            ->where('broker_id', session('broker'))
            ->whereDate('finished_timestamp', '>=', $start)
            ->whereDate('finished_timestamp', '<=', $end)
            ->get([
                'loads.id',
                'loads.driver_id',
                'loads.rate',
                'loads.shipper_rate',
                'loads.carrier_payment_id',
                'loads.shipper_invoice_id',
                'load_statuses.finished_timestamp',
            ]);
        $loadData = [
            'paid_income' => 0,
            'paid_expenses' => 0,
            'pending_income' => 0,
            'pending_expenses' => 0,
            'load_count' => $loads->count(),
            'carriers' => [],
        ];
        foreach ($loads as $load) {
            if (!isset($loadData['carriers'][$load->driver->carrier_id])) {
                $loadData['carriers'][$load->driver->carrier_id] = [
                    'id' => $load->driver->carrier_id,
                    'name' => $load->driver->carrier->name,
                    'quantity' => 0,
                    'income' => 0,
                    'expenses' => 0,
                ];
            }
            $loadData['carriers'][$load->driver->carrier_id]['quantity']++;
            $loadData['carriers'][$load->driver->carrier_id]['income'] += (double)$load->shipper_rate;
            $loadData['carriers'][$load->driver->carrier_id]['expenses'] += (double)$load->rate;

            if ($load->shipper_invoice_id) {
                $loadData['paid_income'] += (double)$load->shipper_rate;
            } else {
                $loadData['pending_income'] += (double)$load->shipper_rate;
            }
            if ($load->carrier_payment_id) {
                $loadData['paid_expenses'] += (double)$load->rate;
            } else {
                $loadData['pending_expenses'] += (double)$load->rate;
            }
        }
        $loadData['carriers'] = array_values($loadData['carriers']);

        $income = Income::where('broker_id', session('broker'))
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->with([
                'type:id,name',
                'account:id,name',
            ])
            ->get([
                'id',
                'type_id',
                'account_id',
                'date',
                'amount',
            ]);

        $expenses = Expense::where('broker_id', session('broker'))
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->with([
                'type:id,name',
                'account:id,name',
            ])
            ->get([
                'id',
                'account_id',
                'date',
                'amount',
            ]);

        return compact('loadData', 'income', 'expenses');
    }
}

