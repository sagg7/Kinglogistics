<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DriverHasUnfinishedLoads;
use App\Exceptions\DriverHasUnfinishedLoadsException;
use App\Http\Controllers\Controller;
use App\Models\AvailableDriver;
use App\Models\Shift;
use App\Traits\Shift\ShiftTrait;
use Illuminate\Http\Request;

class ShiftController extends Controller
{

    use ShiftTrait;

    public function create()
    {
        $driver = auth()->user();
        $payload = [];

        $lastLoad = $driver->loads()->orderBy('id', 'desc')->first();

        if (!empty($lastLoad)) {
            $payload['last_load'] = [
                'box_status' => $lastLoad->box_status_init,
                'box_type_id' => $lastLoad->box_type_id,
                'box_number' => $lastLoad->box_number
            ];
        }

        $truck = $driver->truck;

        if (!empty($truck)) {
            $payload['truck'] = [
                'have_truck' => !empty($truck),
                'truck_number' => $truck->number
            ];

            $trailer = $truck->trailer;


            $payload['chassis'] = [
                'have_chassis' => !empty($trailer),
                // Add relation to chassis_types and retrieve the entry
            ];

        }

        return response($payload);
    }

    public function checkStatus()
    {
        $driver = auth()->user();

        $payload = [
            'active' => !!Shift::where('driver_id', $driver->id)->first()
        ];

        return response(['status' => 'ok', 'data' => $payload]);
    }

    public function start(Request $request)
    {
        $driver = auth()->user();

        // Before create a shift, checks if the driver has already an ongoing shift
        if ($driver->hasActiveShift()) {
            return response([
                'status' => 'error',
                'message' => __('You already have an ongoing shift')
            ], 400);
        }

        // Check if the user can activate its shift, checking current time compared to assigned turn time range
        if (!$driver->canActiveShift()) {
            return response([
                'status' => 'error',
                'message' => __('Your turn is out of time range')
            ], 400);
        }

        // Create a Shift instance just to retrieve the fillable fields
        $shift = new Shift();
        $payload = $request->all($shift->getFillable());

        // Starts shift for this driver
        $this->startShift($driver, $payload);

        return response(['status' => 'ok'], 200);
    }

    public function end(Request $request)
    {
        $driver = auth()->user();

        try {
            // End the driver
            $this->endShift($driver);
        } catch (DriverHasUnfinishedLoadsException $exception) {
            return response(['status' => 'error', 'message' => 'You have unfinished loads']);
        }

        return response(['status' => 'ok'], 200);
    }


}
