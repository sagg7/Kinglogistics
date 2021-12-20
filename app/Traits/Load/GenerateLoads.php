<?php

namespace App\Traits\Load;

use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Load;
use App\Models\LoadStatus;
use App\Models\Trip;
use App\Notifications\LoadAssignment;
use App\Traits\Accounting\PaymentsAndCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait GenerateLoads
{
    use PaymentsAndCollection;

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
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'date' => ['required', 'date'],
            'control_number' => ['required', 'numeric'],
            'origin' => ['required', 'string', 'max:255'],
            'origin_coords' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'destination_coords' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_po' => ['nullable', 'string', 'max:255'],
            'customer_reference' => ['nullable', 'string', 'max:255'],
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


            $trip = Trip::find($data['trip_id']);

            $load->shipper_id = $trip->shipper_id;
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
            // If newly created or updating a load which is not finished
            if (!$load->id || ($load->id && $load->status !== 'finished')) {
                // Get the trip zone id
                $zone_id = $trip->zone_id ?? null;
                // If all corresponding data to get the rate is set, then get the rate
                if (isset($data["mileage"]) && $data["shipper_id"] && $zone_id) {
                    $rate = $this->getRate($data["mileage"], $data["shipper_id"], $zone_id)["rate"];
                    $load->rate = $rate->carrier_rate ?? null;
                    $load->shipper_rate = $rate->shipper_rate ?? null;
                }
            }
            $load->notes = $data["notes"] ?? null;

            if (isset($data['status']))
                $load->status = $data["status"];
            $load->save();
            if (!$id){
                $loadStatus = new LoadStatus();
                $loadStatus->load_id = $load->id;
                $loadStatus->unallocated_timestamp = Carbon::parse($data["date"]);
                if ($load->notes = 'finished'){
                    $loadStatus->unallocated_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->requested_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->accepted_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->loading_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->to_location_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->arrived_timestamp = Carbon::parse($data["date"]);
                    $loadStatus->finished_timestamp = Carbon::parse($data["date"]);
                }

                $loadStatus->save();
            }

            /**
             * As the ID's in data property are Strings, we must reload the load to automatically convert the ids to valid int types
             * This small fix were added to avoid type issues in the mobile app.
             * */
            $load = Load::find($load->id);
            $load->notified_at = Carbon::now()->format('Y-m-d H:i:s');

            if (isset($data["driver_id"]) and $load->notes != 'finished') {
                // Send push notification message to Driver
                $this->notifyToDriver($data["driver_id"], $load);

                // Delete driver from the available driver's lists
                $availableDriver = AvailableDriver::where('driver_id', $data["driver_id"])->first();

                if (!empty($availableDriver))
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
