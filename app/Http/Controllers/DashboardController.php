<?php

namespace App\Http\Controllers;

use App\Imports\CitiesImport;
use App\Models\Driver;
use App\Jobs\ProcessPaymentsAndCollection;
use App\Models\InspectionCategory;
use App\Models\Load;
use App\Models\PaperworkFile;
use App\Models\ShipperInvoice;
use App\Traits\Accounting\PaymentsAndCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Paperwork\PaperworkSendEmailAlert;

class DashboardController extends Controller
{
    use PaymentsAndCollection, PaperworkSendEmailAlert;

    public function index()
    {
        if (auth()->user()->hasRole('dispatch')) {
            return redirect()->route('load.indexDispatch');
        }
        return view('dashboard.dashboard');
    }

    public function getData(Request $request)
    {
        /*$start = Carbon::now()->subMonths(3)->startOfMonth();
       $end = Carbon::now()->endOfMonth()->endOfDay();*/
        //whereBetween('loads.date', [$start, $end])
        $today = new Carbon();
        if ($today->dayOfWeek === Carbon::MONDAY)
            $monday = $today;
        else
            $monday = new Carbon('last monday');

        $monday = $monday->startOfDay();
        $loads = Load::where(function ($q) use ($request) {
            if (auth()->guard('web')->check()) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            }
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
            ->where(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), '>=', $monday)
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
        
        // $days30 = Carbon::now()->addDays(30);
        //     $days15 = Carbon::now()->addDays(15);
        //     $days3 = Carbon::now()->addDays(3);
        //     $days = Carbon::now();
            
        //     $query30 = PaperworkFile::whereDate('expiration_date', $days30)
        //         ->get();
        //     $query15 = PaperworkFile::whereDate('expiration_date', $days15)
        //         ->get();
        //     $query3 = PaperworkFile::whereDate('expiration_date', $days3)
        //         ->get();
        //     $query = PaperworkFile::whereDate('expiration_date', $days)
        //         ->get();
            
        //     // dd($query, $query30,$query15,$query3);
        //     foreach($query30 as $data){
        //         $data['day'] = 30;
        //         $this->NotificationPaperworkAlert($data);
        //     }
        //     foreach($query15 as $data){
        //         $data['day'] = 15;           
        //         $this->NotificationPaperworkAlert($data);    
        //     }
        //     foreach($query3 as $data){
        //         $data['day'] = 3;           
        //         $this->NotificationPaperworkAlert($data);
        //     }
        //     foreach ($query as  $data){
        //         $data['day'] = 0;            
        //         $this->NotificationPaperworkAlert($data);
        //     } 
    }
}
