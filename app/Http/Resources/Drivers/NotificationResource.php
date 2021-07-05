<?php

namespace App\Http\Resources\Drivers;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'data' => $this->type,
            'read' => isset($this->read_at),
            'created_at' => $this->created_at->format('d/m/Y h:i a'),
        ];
    }
}
