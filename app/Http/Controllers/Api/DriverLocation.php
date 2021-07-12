<?php

namespace App\Http\Controllers\Api;

use App\Events\TruckLocationUpdate;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverLocation extends Controller
{

    public function updateDriverLocation(Request $request)
    {
        $driver = auth()->user();

        // ... Do stuf related to location

        $coords = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ];

        event(new TruckLocationUpdate($driver, $coords));

        return response(['status' => 'ok'], 200);
    }


}
