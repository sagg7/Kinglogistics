<?php

namespace App\Traits\Shift;

use App\Models\AvailableDriver;
use App\Models\Shift;

trait ShiftTrait
{

    public function startShift($driver, $payload)
    {
        if (AvailableDriver::where('driver_id', $driver->id)->first()) {
            return response([
                'status' => 'error',
                'message' => __('You already have an ongoing shift')
            ], 403);
        }

        $shift = new Shift();
        $shift->fill($payload);
        $shift->save();

        $availableDriver = new AvailableDriver();
        $availableDriver->driver_id = $driver->id;
        $availableDriver->save();

        return response(['status' => 'ok'], 200);
    }

    public function endShift($driver, $shift)
    {
        AvailableDriver::where('driver_id', $driver->id)->delete();

        // ... Do stuff related to shift ending

        return response(['status' => 'ok'], 200);
    }
}
