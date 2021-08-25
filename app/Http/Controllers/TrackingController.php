<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\LocationGroup;
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
            if (!auth()->user()->hasRole('admin'))
                abort(404);
        }

        $data = Driver::whereHas('latestLocation')
            ->with([
                'latestLocation' => function ($q) {
                    $q->with(['parentLoad' => function ($q) {
                        $q->with([
                            'truck:id,number',
                            'shipper:id,name',
                        ])
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
                else if (auth()->guard('shipper')->check())
                    $q->whereHas('truck', function ($q) use ($user_id) {
                        $q->whereHas('trailer', function ($q) use ($user_id) {
                            $q->where('shipper_id', $user_id);
                        });
                    });
            })
            ->get([
                'drivers.id',
                'drivers.name',
                'drivers.carrier_id',
            ]);

        $params = compact('data', 'channel', 'event');

        return view('loads.tracking', $params);
    }
}
