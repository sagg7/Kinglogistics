<?php

namespace App\Http\Controllers\Api;

use App\Events\TruckLocationUpdate;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{

    public function updateDriverLocation(Request $request)
    {
        $driver = auth()->user();

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $payload = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => $request->get('status'),
            'driver_id' => $driver->id,
            'load_id' => $request->get('load_id'),
        ];

        $driverLocation = new DriverLocation();
        $driverLocation->fill($payload);
        $driverLocation->save();

        event(new TruckLocationUpdate($driver, [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]));

        return response(['status' => 'ok'], 200);
    }


}
