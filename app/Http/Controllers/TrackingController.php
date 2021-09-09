<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Driver;
use App\Models\Load;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if (auth()->guard('carrier')->check()) {
            //$locationGroup = LocationGroup::where('carrier_id', $user_id)->first()->id ?? null;
            $channel = "driver-location-carrier." . $user_id;
            $event = "DriverLocationUpdateForCarrier";
        } else if (auth()->guard('shipper')->check()) {
            //$locationGroup = LocationGroup::where('shipper_id', $user_id)->first()->id ?? null;
            $channel = "driver-location-shipper." . $user_id;
            $event = "DriverLocationUpdateForShipper";
        } else {
            $channel = "driver-location-king";
            $event = "DriverLocationUpdateForKing";
        }

        $data = Driver::whereHas('latestLocation')
            ->with([
                'latestLocation' => function ($q) use ($user_id) {
                    $q->with(['parentLoad' => function ($q) use ($user_id) {
                        $q->with([
                            'truck:id,number',
                            'shipper:id,name',
                        ])
                            ->where(function ($q) use ($user_id) {
                                if (auth()->guard('shipper')->check())
                                    $q->whereHas('truck', function ($q) use ($user_id) {
                                        $q->whereHas('trailer', function ($q) use ($user_id) {
                                            $q->where('shipper_id', $user_id);
                                        });
                                    });
                            })
                            ->select([
                                'id', 'origin', 'destination', 'shipper_id', 'truck_id',
                            ]);
                    }]);
                },
                'carrier:id,name',
                'truck:id,number,driver_id',
            ])
            /*->whereHas('latestLocation', function ($q) {
                $q->where('status', '!=', 'finished');
            })*/
            ->whereHas("shift")
            ->whereNull("inactive")
            ->where(function ($q) use ($user_id) {
                if (auth()->guard('carrier')->check())
                    $q->where('carrier_id', $user_id);
            })
            ->get([
                'drivers.id',
                'drivers.name',
                'drivers.carrier_id',
            ]);

        $company = Broker::select('name', 'contact_phone', 'email', 'address', 'location')->find(1);

        $params = compact('data', 'channel', 'event', 'company');

        return view('tracking.tracking', $params);
    }

    public function history()
    {
        $user_id = auth()->user()->id;

        $data = Driver::with([
            'locations',
            'carrier:id,name',
            'truck:id,number,driver_id',
        ])
            ->where(function ($q) use ($user_id) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas('locations', function ($q) use ($user_id) {
                        $q->whereHas('parentLoad', function ($q) use ($user_id) {
                            $q->whereHas('trip', function ($q) use ($user_id) {
                                $q->where('shipper_id', $user_id);
                            });
                        });
                    });
                }
            })
            ->get();

        $company = Broker::select('name', 'contact_phone', 'email', 'address', 'location')->find(1);

        $params = compact('data',  'company');

        return view('tracking.history', $params);
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
