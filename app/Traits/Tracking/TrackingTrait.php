<?php

namespace App\Traits\Tracking;

use App\Models\Broker;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

trait TrackingTrait
{
    private function getTrackingData()
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

        $data = Driver::whereNull("drivers.inactive")
            ->where(function ($q) use ($user_id) {
                if (auth()->guard('carrier')->check())
                    $q->where('drivers.carrier_id', $user_id);
            })
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
            //->whereHas('locations')
            ->with([
                /*'latestLocation' => function ($q) use ($user_id) {
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
                },*/
                'carrier:id,name',
                'truck:id,number,driver_id',
                'shift',
            ])
            ->join('driver_locations', function ($q) {
                $q->on('driver_locations.driver_id', '=', 'drivers.id')
                    ->on('driver_locations.id', '=', DB::raw("(select max(id) from driver_locations WHERE driver_locations.driver_id = drivers.id)"));
            })
            ->leftjoin('loads', 'loads.id', '=', 'driver_locations.load_id')
            ->leftjoin('trucks', 'trucks.id', '=', 'loads.truck_id')
            ->leftjoin('shippers', function ($q) use ($user_id) {
                $q->on('shippers.id', '=', 'loads.shipper_id');
                if (auth()->guard('shipper')->check())
                    $q->where('shippers.id', $user_id);
            })
            ->get([
                'drivers.id',
                'drivers.broker_id',
                'drivers.name',
                'drivers.carrier_id',
                'driver_locations.id as location_id',
                'driver_locations.latitude',
                'driver_locations.longitude',
                'driver_locations.status as location_status',
                'driver_locations.created_at as location_date',
                'loads.origin',
                'loads.destination',
                'loads.shipper_id',
                'loads.truck_id',
                'trucks.number as truck_number',
                'shippers.name as shipper_name',
            ]);

        $company = Broker::select('name', 'contact_phone', 'email', 'address', 'location')->find(session('broker') ?? auth()->user()->broker_id);

        return compact('data', 'channel', 'event', 'company');
    }
}
