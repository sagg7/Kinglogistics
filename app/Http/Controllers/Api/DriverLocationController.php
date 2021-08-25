<?php

namespace App\Http\Controllers\Api;

use App\Events\DriverLocationUpdateForCarrier;
use App\Events\DriverLocationUpdateForShipper;
use App\Events\DriverLocationUpdateForKing;
use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\LocationGroup;
use App\Models\Driver;
use App\Models\DriverLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverLocationController extends Controller
{

    public function updateDriverLocation(Request $request)
    {
        $driver = auth()->user();
        $loadId = $request->get('load_id');

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $payload = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => $request->get('status'),
            'driver_id' => $driver->id,
            'load_id' => $loadId,
        ];

        $driverLocation = new DriverLocation();
        $driverLocation->fill($payload);
        //$driverLocation->save();

        $load = Load::find($loadId);

        $coords = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        // TODO: CHECK THIS! Does driver could have more than one shipper????
        $shipper = empty($load) ? $driver->shippers->first() : $load->shipper;

        // Broadcast event for shippers channel
        event(new DriverLocationUpdateForShipper(
                $driver,
                $shipper,
                LocationGroup::where('shipper_id', $shipper->shipper_id)->first(),
                $coords,
                $request->get('status'))
        );

        // Broadcast event for carriers channel
        event(new DriverLocationUpdateForCarrier(
                $driver,
                $driver->carrier,
                LocationGroup::where('carrier_id', $driver->carrier_id)->first(),
                $coords,
                $request->get('status'))
        );

        // Broadcast event for king (admin) channel
        event(new DriverLocationUpdateForKing($driver, $coords, $request->get('status')));

        return response(['status' => 'ok'], 200);
    }

}
