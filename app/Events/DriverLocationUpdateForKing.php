<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdateForKing implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver;
    public $coords;
    public $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($driver, $coords, $status)
    {
        $this->driver = $driver;
        $this->coords = $coords;
        $this->status = $status;
    }

    public function broadcastWith()
    {
        return [
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'truck' => [
                    'number' => $this->driver->truck->number,
                ],
                'shift' => $this->driver->shift->id,
            ],
            'carrier' => [
                'id' => $this->driver->carrier->id,
                'name' => $this->driver->carrier->name,
            ],
            'shippers' => $this->driver
                ->shippers
                ->map
                ->only('id', 'name'),
            'coords' => $this->coords,
            'status' => $this->status
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Only users authenticated as admins can join this channel
        return new PrivateChannel('driver-location-king');
    }
}
