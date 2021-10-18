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
        $company = Broker::select('name', 'contact_phone', 'email', 'address', 'location')->find(1);

        $params = compact('company');

        return view('tracking.history', $params);
    }

    public function historyData(Request $request)
    {
        $user_id = auth()->user()->id;
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        return Driver::with([
            'locations',
            'carrier:id,name',
            'truck:id,number,driver_id',
        ])
            ->where(function ($q) use ($user_id, $start, $end) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas('locations', function ($q) use ($user_id, $start, $end) {
                        $q->whereHas('parentLoad', function ($q) use ($user_id) {
                            $q->whereHas('trip', function ($q) use ($user_id) {
                                $q->where('shipper_id', $user_id);
                            });
                        })
                            ->whereBetween('created_at', [$start, $end]);
                    });
                } else if (auth()->guard('carrier')->check()) {
                    $q->where('carrier_id', auth()->user()->id);
                }
                $q->whereHas('locations', function ($q) {
                    $q->whereNotNull('load_id');
                });
            })
            ->withTrashed()
            ->get()->take(1000);
    }

    public function getPinLoadData(Request $request)
    {
        return Load::with([
            'truck:id,number',
            'shipper:id,name',
        ])
            ->where(function ($q) {
                if (auth()->guard('shipper')->check())
                    $q->whereHas('trip', function ($q) {
                        $q->where('shipper_id', auth()->user()->id);
                    });
            })
            ->findOrFail($request->load);
    }
}
