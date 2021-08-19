<?php

namespace App\Traits\Load;

use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Load;
use App\Notifications\LoadAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait GenerateLoads
{
    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'load_number' => ['sometimes', 'numeric', 'min:1', 'max:999'],
            'shipper_id' => ['sometimes', 'exists:shippers,id'],
            'trip_id' => ['required', 'exists:trips,id'],
            'load_type_id' => ['required', 'exists:load_types,id'],
            'driver_id' => ['sometimes', 'required', 'exists:drivers,id'],
            'date' => ['required', 'date'],
            'control_number' => ['required', 'string', 'max:255'],
            'origin' => ['required', 'string', 'max:255'],
            'origin_coords' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'destination_coords' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_po' => ['required', 'string', 'max:255'],
            'customer_reference' => ['required', 'string', 'max:255'],
            'tons' => ['nullable', 'string', 'max:255'],
            'silo_number' => ['nullable', 'string', 'max:255'],
            'container' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric'],
            'mileage' => ['nullable', 'numeric'],
            'status' => ['sometimes', 'required'],
        ], [
            'origin_coords.required' => 'The origin map location is required',
            'destination_coords.required' => 'The destination map location is required',
        ], [
            'load_type_id' => 'load type',
            'driver_id' => 'driver',
        ]);
    }

    /**
     * @param array $data
     * @param int|null $id
     * @return Load
     */
    private function storeUpdate(array $data, int $id = null): Load
    {
        return DB::transaction(function () use ($data, $id) {
            if ($id)
                $load = Load::findOrFail($id);
            else
                $load = new Load();

            if (isset($data['shipper_id']))
                $load->shipper_id = $data["shipper_id"];
            $load->load_type_id = $data["load_type_id"];
            if (isset($data['driver_id'])) {
                $load->driver_id = $data["driver_id"] ?? null;
                $load->truck_id = isset($data["driver_id"]) ? Driver::with('truck')->find($data["driver_id"])->truck->id ?? null : null;
            }
            $load->load_log_id = $data["load_log_id"] ?? null;
            $load->trip_id = $data["trip_id"] ?? null;
            $load->date = Carbon::parse($data["date"]);
            $load->control_number = $data["control_number"];
            $load->origin = $data["origin"];
            $load->origin_coords = $data["origin_coords"];
            $load->destination = $data["destination"];
            $load->destination_coords = $data["destination_coords"];
            $load->customer_name = $data["customer_name"];
            $load->customer_po = $data["customer_po"];
            $load->customer_reference = $data["customer_reference"];
            $load->tons = $data["tons"] ?? null;
            $load->silo_number = $data["silo_number"] ?? null;
            $load->container = $data["container"] ?? null;
            $load->weight = $data["weight"] ?? null;
            $load->mileage = $data["mileage"] ?? null;
            $load->rate = $data["rate"] ?? null;
            $load->shipper_rate = $data["shipper_rate"] ?? null;
            $load->notes = $data["notes"] ?? null;
            if (isset($data['status']))
                $load->status = $data["status"];
            $load->save();

            if (isset($data["driver_id"])) {
                // Send push notification message to Driver
                $this->notifyToDriver($data["driver_id"], $load);

                // Delete driver from the available driver's lists
                $availableDriver = AvailableDriver::where('driver_id', $data["driver_id"])->first();
                $availableDriver->delete();
            }

            return $load;
        });
    }

    /**
     * Creates and send a Load Assignment notification to driver
     *
     * @param $driverId
     * @param $load
     */
    private function notifyToDriver($driverId, $load)
    {
        $driver = Driver::find($driverId);

        $driver->notify(new LoadAssignment($driver, $load));
    }
}
