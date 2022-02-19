<?php

namespace App\Http\Controllers;

use App\Models\Load;
use App\Models\Shipper;
use App\Models\Trip;
use App\Models\User;
use App\Models\LoadStatus;
use App\Models\dispatch_report;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function dailyLoads(Request $request)
    {
        return view('reports.dailyLoads');
    }

    public function createDailyDispatchReport(Load $load,Driver $driver,LoadStatus $Load_statuse ){
        $name = auth()->user()->name;
        $userId = auth()->user()->id;
        $now = Carbon::now();
        $params = [
            'name'=> $name,
            'date'=> $now->format('d-m-Y'),
            'hours' => $now->toTimeString(),
            'active_loads' => $load::where('status','!=','finished')
                            ->where('broker_id', session('broker'))->count(),
            'active_drivers' => $driver::where('status','!=','inactive')
                            ->where('broker_id', session('broker'))->count(),
            'inactive_drivers' => $driver::where('status','==','inactive')
                            ->where('broker_id', session('broker'))->count(),
            'loads_finalized' => $load::where('dispatch_id',$userId)
                            ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                            ->whereBetween('load_statuses.finished_timestamp', [$now->subHours(12), $now])
                             ->where('broker_id', session('broker'))->count(),
            // 'loads_finalized' =>1,
            'worked_time' => 60.60,
            "dispatch_score" => 98,
            'score_app_usage'=> 92.22,
            // Session::where('user_id',$userId)->first()->date
            // 'tiempo' => $now->toDateTimeString()
        ];
        return $params;
    }


    public function storeDispatchReport(Request $request, Load $load,Driver $driver,LoadStatus $Load_statuse){ 
        $dispatch_report = new dispatch_report();
        $name = auth()->user()->name;
        $userId = auth()->user()->id;
        $now = Carbon::now();
        $dispatch_report->dispatch_id = auth()->user()->id;  
        $dispatch_report->date = Carbon::now();  
        $dispatch_report->active_loads = $load::where('status','!=','finished')
        ->where('broker_id', session('broker'))->count();  
        $dispatch_report->active_drivers = $driver::where('status','!=','inactive')
        ->where('broker_id', session('broker'))->count();  
        $dispatch_report->inactive_drivers = $driver::where('status','==','inactive')
        ->where('broker_id', session('broker'))->count();  
        $dispatch_report->well_status = $request->wellStatus;  
        $dispatch_report->loads_finalized = $load::where('dispatch_id',$userId)
        ->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
        ->whereBetween('load_statuses.finished_timestamp', [$now->subHours(12), $now])
         ->where('broker_id', session('broker'))->count();  
        $dispatch_report->worked_time = 60.60;  
        // $dispatch_report->worked_time = $request->worked_time;  
        $dispatch_report->dispatch_score = 98;  
        // $dispatch_report->dispatch_score = $request->dispatch_score;  
        $dispatch_report->score_app_usage = 92.22;
        // $dispatch_report->score_app_usage = $request->score_app_usage;
        $dispatch_report->description = $request->situationDescription;  
        $dispatch_report->save();
        
        return ['success' => true, 'data' => $dispatch_report];
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
        $goal = null;
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
        $contador = null;
        foreach($data as $key => $item){

            if($key  == 0){
                $average[] = $item; 
             $total = $item;
            }elseif ($key > 30){
                $total += $item - $data [$key - 30];
                $average[] = Round($total / 30);
            }
            else {
                $total += $item;
                $average[] = Round($total / ($key +1));
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
}

