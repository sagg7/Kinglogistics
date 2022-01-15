<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Driver;
use App\Models\Load;
use App\Traits\Tracking\TrackingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use TrackingTrait;

    public function index()
    {
        $params = $this->getTrackingData();

        return view('tracking.tracking', $params);
    }

    public function history()
    {
        $company = Broker::select('name', 'contact_phone', 'email', 'address', 'location')->find(session('broker'));

        $params = compact('company');

        return view('tracking.history', $params);
    }

    public function historyData(Request $request)
    {
        $user_id = auth()->user()->id;
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $driverId = $request->driver_id;

        return Driver::with([
            'carrier:id,name',
            'truck:id,number,driver_id',
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
            ->with('locations', function($q) use ($start, $end){
                $q->whereBetween('created_at', [$start, $end])->take(1000);
            })
            ->where(function ($q) use ($user_id, $start, $end, $driverId) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas('locations', function ($q) use ($user_id, $start, $end) {
                        $q->whereHas('parentLoad', function ($q) use ($user_id) {
                            $q->whereHas('trip', function ($q) use ($user_id) {
                                $q->where('shipper_id', $user_id);
                            });
                        });
                    });
                } else if (auth()->guard('carrier')->check()) {
                    $q->where('carrier_id', auth()->user()->id);
                }
                $q->whereHas('locations', function ($q) {
                    $q->whereNotNull('load_id');
                });
                if ($driverId)
                    $q->where("id", $driverId);
            })->get();
    }

    public function getPinLoadData(Request $request)
    {
        $load = Load::with([
            'truck:id,number',
            'shipper:id,name',
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
            ->where(function ($q) {
                if (auth()->guard('shipper')->check())
                    $q->whereHas('trip', function ($q) {
                        $q->where('shipper_id', auth()->user()->id);
                    });
            })
            ->find($request->load);

        if ($load)
            return $load;
        else
            return Driver::with([
                'truck:id,number',
                'shipper:id,name',
            ])
                ->where(function ($q) {
                    if (auth()->guard('shipper')->check())
                        $q->whereHas('trip', function ($q) {
                            $q->where('shipper_id', auth()->user()->id);
                        });
                })
                ->find($request->load);
    }
}
