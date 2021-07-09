<?php

namespace App\Http\Resources\Broadcasting;

use Illuminate\Http\Resources\Json\JsonResource;

class TruckLocationUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'driver' => ''
        ];
    }
}
