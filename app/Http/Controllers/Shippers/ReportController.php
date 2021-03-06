<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\LoadStatus;
use App\Models\Trailer;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Clue\StreamFilter\fun;

class ReportController extends Controller
{
    private function tripsSelect()
    {
        return ['trips' => [null => ''] + Trip::where('shipper_id', auth()->user()->id)->pluck('name', 'id')->toArray()];
    }

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
                'chassis_type:id,name',
            ])
            ->get();
    }

    public function trips()
    {
        $params = $this->tripsSelect();
        return view('subdomains.shippers.reports.trips', $params);
    }

    public function tripsData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        return Trip::where(function ($q) use ($request) {
                if ($request->trip)
                    $q->where('trips.id', $request->trip);
            })
            ->with('loads', function($q) use ($start, $end) {
                $q->with('load_type:id,name')
                    ->where('shipper_id', auth()->user()->id)
                    ->whereBetween('date', [$start, $end])
                    ->select('id', 'load_type_id', 'trip_id', 'date');
            })
            ->where('shipper_id', auth()->user()->id)
            ->get(['id', 'name']);
    }

    public function loads()
    {
        $params = $this->tripsSelect();
        return view('subdomains.shippers.reports.loads', $params);
    }

    public function loadsData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfWeek();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();

        return Driver::whereHas('loads', function ($q) use ($request, $start, $end) {
            $q->where('shipper_id', auth()->user()->id)
                ->where('status', 'finished')
                ->whereBetween('date', [$start, $end]);

            $q->where(function ($q) use ($request) {
                if ($request->trip)
                    $q->where('loads.trip_id', $request->trip);
            });
        })
            ->withCount(['loads' => function ($q) use ($request) {
                $q->where('shipper_id', auth()->user()->id)
                    ->where('status', 'finished');
            }])
            ->with(['loads' => function ($q) use ($request) {
                $q->orderBy('date')
                ->select(['id','date','driver_id','control_number','origin','destination'])
                ->where('shipper_id', auth()->user()->id)
                    ->where('status', 'finished');
            }])
            ->get(['id', 'name']);
    }

    public function AvgTimePerLoad($shipper = null){
        $loads = LoadStatus::get(['accepted_timestamp', 'finished_timestamp']);
        if ($shipper != null){
           // $loads->where()
        }
    }
}
