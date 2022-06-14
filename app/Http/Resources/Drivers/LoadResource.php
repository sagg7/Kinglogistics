<?php

namespace App\Http\Resources\Drivers;

use Illuminate\Http\Resources\Json\JsonResource;

class LoadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "load_type_id" => $this->load_type_id,
            "driver_id" => $this->driver_id,
            "shipper_id" => $this->shipper_id,
            "load_log_id" => $this->load_log_id,
            "date" => $this->date,
            "control_number" => $this->control_number,
            "origin" => $this->origin,
            "origin_id" => $this->origin_id,
            "origin_coords" => $this->origin_coords,
            "destination" => $this->destination,
            "destination_id" => $this->destination_id,
            "destination_coords" => $this->destination_coords,
            "customer_name" => $this->customer_name,
            "customer_po" => $this->customer_po,
            "customer_reference" => $this->customer_reference,
            "tons" => $this->tons,
            "silo_number" => $this->silo_number,
            "container" => $this->container,
            "weight" => $this->weight,
            "mileage" => $this->mileage,
            "status" => $this->status,
            "auto_assigned" => $this->auto_assigned,
            "notified_at" => $this->getNotifiedAtProperty(),
            "load_status" => new LoadStatusResource($this->loadStatus),
            "shipper_name" => $this->shipper->name ?? null,
            "description" => $this->description,
            "created_at" => $this->created_at,
        ];
    }
}
