<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvailableDriver;
use App\Models\Shift;
use App\Traits\Shift\ShiftTrait;
use Illuminate\Http\Request;

class ShiftController extends Controller
{

    use ShiftTrait;

    public function checkStatus()
    {
        $driver = auth()->user();

        $payload = [
            'active' => !!AvailableDriver::where('driver_id', $driver->id)->first()
        ];

        return response(['status' => 'ok', 'data' => $payload]);
    }

    public function start(Request $request)
    {
        $driver = auth()->user();

        // Before create a shift, checks if the driver has already an ongoing shift¬
        if (AvailableDriver::where('driver_id', $driver->id)->first()) {
            return response([
                'status' => 'error',
                'message' => __('You already have an ongoing shift')
            ], 403);
        }

        // Create a Shift instance just to retrieve the fillable fields
        $shift = new Shift();

        $payload = $request->all($shift->getFillable());
        $payload['driver_id'] = $driver->id;

        $this->startShift($driver, $payload);

        return response(['status' => 'ok'], 200);
    }

    public function end(Request $request)
    {
        $driver = auth()->user();
        $shift = Shift::find($request->shift_id);

        $this->endShift($driver, $shift);

        return response(['status' => 'ok'], 200);
    }


}
