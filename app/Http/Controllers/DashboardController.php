<?php

namespace App\Http\Controllers;

use App\Models\Load;
use App\Traits\Accounting\PaymentsAndCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PaymentsAndCollection;
    public function index()
    {
        return view('dashboard.dashboard');
    }

    public function getData(Request $request)
    {
        $start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();
        $loads = Load::whereBetween('loads.date', [$start, $end])
            ->where(function ($q) {
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
                else if (auth()->guard('carrier')->check())
                    $q->whereHas('driver', function ($q) {
                        $q->where('carrier_id', auth()->user()->id);
                    });
            })
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
            ])
            ->get([
                'id',
                'origin',
                'destination',
                'shipper_id',
                'driver_id',
                'truck_id',
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
        //$this->shipperInvoices();
        //dd(Carbon::now()->subDays(2)->weekday());
        /*DB::transaction(function () {
            $this->chargeRentals();
        });*/
    }
}
