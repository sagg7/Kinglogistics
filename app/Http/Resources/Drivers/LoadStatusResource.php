<?php

namespace App\Http\Resources\Drivers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $dateFormat = 'm/d/Y h:i a';

        return [
            'id' => $this->id,
            'load_id' => $this->load_id,
            'unallocated_timestamp' => isset($this->unallocated_timestamp) ? Carbon::parse($this->unallocated_timestamp)->format($dateFormat) : null,
            'requested_timestamp' => isset($this->requested_timestamp) ? Carbon::parse($this->requested_timestamp)->format($dateFormat) : null,
            'accepted_timestamp' => isset($this->accepted_timestamp) ? Carbon::parse($this->accepted_timestamp)->format($dateFormat) : null,
            'loading_timestamp' => isset($this->loading_timestamp) ? Carbon::parse($this->loading_timestamp)->format($dateFormat) : null,
            'to_location_timestamp' => isset($this->to_location_timestamp) ? Carbon::parse($this->to_location_timestamp)->format($dateFormat) : null,
            'arrived_timestamp' => isset($this->arrived_timestamp) ? Carbon::parse($this->arrived_timestamp)->format($dateFormat) : null,
            'unloading_timestamp' => isset($this->unloading_timestamp) ? Carbon::parse($this->unloading_timestamp)->format($dateFormat) : null,
            'finished_timestamp' => isset($this->finished_timestamp) ? Carbon::parse($this->finished_timestamp)->format($dateFormat) : null,
            'to_location_voucher_image_url' => $this->getToLocationVoucherImageUrlAttribute(),
            'finished_voucher_image_url' => $this->getFinishedVoucherImageUrlAttribute(),
        ];
    }
}
