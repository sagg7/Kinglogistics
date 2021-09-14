<?php

namespace App\Http\Resources\Drivers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'inactive' => $this->phone,
            'carrier' => new CarrierResource($this->carrier),
            'zone' => new ZoneResource($this->zone),
            'is_shift_active' => $this->isShiftActive(),
            'has_active_loads' => $this->hasActiveLoads(),
            'created_at' => $this->created_at->format('m/d/Y'),
        ];
    }
}
