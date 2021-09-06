<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Trailer;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function trailers()
    {
        return view('subdomains.shippers.reports.trailers');
    }

    public function trailersData()
    {
        return Trailer::whereHas('shippers', function ($q) {
            $q->where('id', auth()->user()->id);
        })
            /*->whereHas('truck.driver', function ($q) {
                $q->whereHas('latestLoad', function ($q) {
                    $q->where('box_status_end', "loaded");
                });
            })*/
            ->with([
                'truck' => function ($q) {
                    $q->with(['driver' => function ($q) {
                        $q->with(['latestLoad' => function ($q) {
                            $q->with('boxEnd:id,name')
                                ->select([
                                    'id',
                                    'driver_id',
                                    'box_type_id_end',
                                    'box_number_end',
                                    'box_status_end',
                                ]);
                        }])
                            ->select([
                                'drivers.id',
                                'drivers.name',
                            ]);
                    }])
                        ->select([
                            'trucks.id',
                            'trucks.trailer_id',
                            'trucks.driver_id',
                            'trucks.number',
                        ]);
                },
                'chassisType:id,name',
            ])
            ->get();
    }

    public function trips()
    {
        $trips = [null => ''] + Trip::where('shipper_id', auth()->user()->id)->pluck('name', 'id')->toArray();
        $params = compact('trips');
        return view('subdomains.shippers.reports.trips', $params);
    }

    public function tripsData(Request $request)
    {
        $start = Carbon::parse($request->start) ?? Carbon::now()->startOfMonth();
        $end = Carbon::parse($request->end)->endOfDay() ?? Carbon::now()->endOfMonth()->endOfDay();

        return Trip::whereHas('loads', function ($q) use ($start, $end) {
            $q->where('shipper_id', auth()->user()->id)
                ->whereBetween('date', [$start, $end]);
        })
            ->where(function ($q) use ($request) {
                if ($request->trip)
                    $q->where('trips.id', $request->trip);
            })
            ->with('loads', function($q) {
                $q->with('load_type:id,name')
                    ->select('id', 'load_type_id', 'trip_id');
            })
            ->get(['id', 'name'])->toArray();
    }
}
