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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverLocationController extends Controller
{

    public function updateDriverLocation(Request $request)
    {
        $driver = auth()->user();

        $now = Carbon::parse($request->timestamp) ?? Carbon::now();
        $data = $request->all();
        $lastPosition = count($data) - 1;
        $array = [];
        $lastLocation = [];

        // This would handle the old way the location was received, with only one object for a single location
        if ($request->latitude) {
            $array = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $request->status,
                'driver_id' => $driver->id,
                'load_id' => $request->load_id,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $lastLocation = $array + ['speed' => $request->speed ?? null];
        } else {
            // And this handles the new method, in which we receive an array of locations
            foreach ($data as $i => $item) {
                $payload = [
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'status' => $item['status'],
                    'driver_id' => $driver->id,
                    'load_id' => $item['load_id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $array[] = $payload;

                if ($i === $lastPosition) {
                    $lastLocation = $payload + ['speed' => $item['speed']];
                }
            }
        }
        DriverLocation::insert($array);

        $load = Load::find($lastLocation['load_id']);

        $coords = [
            'latitude' => $lastLocation['latitude'],
            'longitude' => $lastLocation['longitude'],
        ];

        $speed = $lastLocation['speed'];

        // TODO: CHECK THIS! Does driver could have more than one shipper????
        $shipper = empty($load) ? $driver->shippers->first() : $load->shipper;

        // Broadcast event for shippers channel
        event(new DriverLocationUpdateForShipper(
                $driver,
                $shipper,
                LocationGroup::where('shipper_id', $shipper->id)->first(),
                $coords,
                $speed,
                $request->get('status'))
        );

        // Broadcast event for carriers channel
        event(new DriverLocationUpdateForCarrier(
                $driver,
                $driver->carrier,
                LocationGroup::where('carrier_id', $driver->carrier_id)->first(),
                $coords,
                $speed,
                $request->get('status'))
        );

        // Broadcast event for king (admin) channel
        event(new DriverLocationUpdateForKing($driver, $coords, $speed, $request->get('status')));

        return response(['status' => 'ok'], 200);
    }

}
