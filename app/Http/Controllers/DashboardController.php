<?php

namespace App\Http\Controllers;

use App\Models\Load;
use App\Traits\Accounting\PaymentsAndCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use PaymentsAndCollection;

    public function index()
    {
        return view('dashboard.dashboard');
    }

    public function getData(Request $request)
    {
        /*$start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();*/
        //whereBetween('loads.date', [$start, $end])

        $monday = new Carbon('last monday');
        $monday = $monday->format('Y/m/d')." 00:00:00";
        $loads = Load::where(function ($q) use ($request) {
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
                else if (auth()->guard('carrier')->check())
                    $q->whereHas('driver', function ($q) {
                        $q->where('carrier_id', auth()->user()->id);
                    });
                if ($request->shipper) {
                    $q->where('shipper_id', $request->shipper);
                }
                if ($request->trip) {
                    $q->where('trip_id', $request->trip);
                }
                if ($request->driver) {
                    $q->where('driver_id', $request->driver);
                }
            })
            ->join('load_statuses', 'loads.id', '=', 'load_id')
            ->where(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), '>', $monday)
            ->with([
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
            ])
            ->get([
                'loads.id',
                'date',
                'origin',
                'destination',
                'control_number',
                'customer_name',
                'customer_po',
                'customer_reference',
                'weight',
                'tons',
                'silo_number',
                'mileage',
                'shipper_id',
                'driver_id',
                'truck_id',
                'load_type_id',
                'status',
            ]);

        $loadsSummary = [];
        foreach ($loads as $load) {
            if (isset($loadsSummary[$load->status]))
                $loadsSummary[$load->status]["count"]++;
            else
                $loadsSummary[$load->status]["count"] = 1;
            $loadsSummary[$load->status]["data"][] = $load;
        }

        return [
            'loads' => $loadsSummary
        ];
    }

    public function testKernel()
    {
        //$this->carrierPayments();
        //ProcessPaymentsAndCollection::dispatch()->afterCommit();
    }
}
