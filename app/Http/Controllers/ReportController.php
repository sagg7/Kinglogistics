<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailyLoads(Request $request)
    {
        return view('reports.dailyLoads');
    }

    private function compareDates($a, $b) {
        //$a = Carbon::parse($a);
        return $a->isAfter($b);
    }

    public function dailyLoadsData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $query = Trip::whereHas('loads', function ($q) use ($start, $end) {
            $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                ->whereDate('finished_timestamp', '>=', $start)
                ->whereDate('finished_timestamp', '<=', $end);
        })
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->with([
                'loads' => function ($q) use ($start, $end) {
                    $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                        ->whereDate('finished_timestamp', '>=', $start)
                        ->whereDate('finished_timestamp', '<=', $end)
                        ->orderBy('finished_timestamp')
                        ->select(['loads.id', 'trip_id', 'finished_timestamp']);
                },
                'shipper:id,name',
            ])
            ->withTrashed()
            ->where(function ($q) use ($request) {
                if ($request->shipper)
                    $q->where('shipper_id', $request->shipper);
            })
            ->get(['id','name','shipper_id']);

        $dates = [];
        $series = [];
        // For each trip
        foreach ($query as $trip) {
            $count = [];
            // Loop through the finished loads of the trip
            foreach ($trip->loads as $load) {
                // Set the date formatted for the array position and dates array
                $formatted = Carbon::parse($load->finished_timestamp)->format('Y-m-d');
                // If this date hasn't been previously pushed to the dates array, push it
                if (!in_array($formatted, $dates))
                    $dates[] = $formatted;
                // If the date position already exists for the dates count array, sum +1
                if (isset($count[$formatted]))
                    $count[$formatted]++;
                else // else set is initial value as 1
                    $count[$formatted] = 1;
            }

            switch ($request->graph_type) {
                default:
                case "trips": // Format the data per trip
                    // Store the name of the trip and the dates count data on the array
                    $series[] = [
                        'name' => $trip->name,
                        'data' => $count,
                    ];
                    break;
                case "shippers": // Format the data per shipper (customer)
                    if (isset($series[$trip->shipper_id])) {
                        foreach ($count as $date => $item) {
                            // If the data for the date exists, sum the count to the previous value
                            if (isset($series[$trip->shipper_id]["data"][$date]))
                                $series[$trip->shipper_id]["data"][$date] += $item;
                            else
                                // else store the value
                                $series[$trip->shipper_id]["data"][$date] = $item;
                        }
                    } else {
                        // Store the name of the shipper and the dates count data on the array
                        $series[$trip->shipper_id] = [
                            'name' => $trip->shipper->name,
                            'data' => $count,
                        ];
                    }
                    break;
            }
        }
        if ($request->graph_type === "shippers")
            $series = array_values($series);
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
            $dates[$key] = Carbon::parse($date)->format('M d');
        }

        return ['series' => $series, 'categories' => $dates];
    }
}
