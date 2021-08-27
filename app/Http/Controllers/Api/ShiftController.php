<?php

namespace App\Http\Controllers\Api;

use App\Enums\LoadStatusEnum;
use App\Exceptions\DriverHasUnfinishedLoadsException;
use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Shift;
use App\Notifications\LoadAssignment;
use App\Traits\Load\ManageLoadProcessTrait;
use App\Traits\Shift\ShiftTrait;
use Illuminate\Http\Request;

class ShiftController extends Controller
{

    use ShiftTrait, ManageLoadProcessTrait;

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

            if (!empty($trailer)) {
                $payload['chassis'] = [
                    'have_chassis' => true,
                    'trailer_number' => $trailer->number,
                    'chassis_type_id' => $trailer->chassis_type_id,
                // Add relation to chassis_types and retrieve the entry
            ];
            }
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

        // Check if exists unallocated loads and auto assign to driver
        $load = $this->autoAssignUnallocatedLoad($driver);

        // Starts shift for this driver
        $this->startShift($driver, $payload, $load);

        return response(['status' => 'ok', 'assigned_load' => $load], 200);
    }

    /**
     * @throws DriverHasUnfinishedLoadsException
     */
    public function end(Request $request)
    {
        $driver = auth()->user();

        $this->endShift($driver);

        return response(['status' => 'ok'], 200);
    }

    private function autoAssignUnallocatedLoad($driver): ?Load
    {
        $load = Load::where([
            ['status', LoadStatusEnum::UNALLOCATED],
            ['driver_id', null]
        ])
            ->first();

        if (!empty($load)) {
            $assignedStatus = LoadStatusEnum::REQUESTED;
            // Assign the driver and update the entry
            $load->driver_id = $driver->id;
            $load->status = $assignedStatus;
            $load->auto_assigned = true;
            $load->update();

            // Update load status
            $this->switchLoadStatus($load->id, $assignedStatus);

            // Notify to the driver of assignment
            $driver->notify(new LoadAssignment($driver, $load));
        }

        return $load;
    }

}
