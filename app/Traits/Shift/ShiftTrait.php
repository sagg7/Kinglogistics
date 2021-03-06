<?php

namespace App\Traits\Shift;

use App\Enums\LoadStatusEnum;
use App\Exceptions\DriverHasUnfinishedLoadsException;
use App\Models\AvailableDriver;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait ShiftTrait
{

    public function startShift($driver, $payload, $load)
    {
        // Create and assign the shift to provided driver
        $driver->shift()->create($payload);

        $driver->workedHour()->create();

        if (!$load) {
            $this->registryInAvailableDriversQueue($driver);
        }
    }

    /**
     * @throws DriverHasUnfinishedLoadsException
     */
    public function endShift($driver)
    {
        $unfinishedLoads = $driver->loads->filter(function ($load) {
            return !in_array($load->status, [LoadStatusEnum::FINISHED, LoadStatusEnum::UNALLOCATED]);
        });

        if (count($unfinishedLoads) > 0) {
            throw new DriverHasUnfinishedLoadsException;
        }

        if (!empty($driver->availableDriver)) {
            AvailableDriver::destroy($driver->availableDriver->id);
        }

        if (!empty($driver->shift)) {
            Shift::destroy($driver->shift->id);
            $driver->load('activeWorkedHour');
            if ($driver->activeWorkedHour) {
                $now = Carbon::now();
                $minutesWorked = Carbon::parse($driver->activeWorkedHour->shift_start)->diffInMinutes($now);
                $driver->activeWorkedHour->shift_end = $now;
                $driver->activeWorkedHour->worked_hours = $minutesWorked / 60;
                $driver->activeWorkedHour->save();
            }
        }
        $driver->status = 'inactive';
        $driver->save();
        return response(['status' => 'ok'], 200);
    }

    public function registryInAvailableDriversQueue($driver) {
        // Registry the driver in the available drivers table
        $availableDriver = new AvailableDriver();
        $availableDriver->driver_id = $driver->id;
        $availableDriver->save();
    }
}
