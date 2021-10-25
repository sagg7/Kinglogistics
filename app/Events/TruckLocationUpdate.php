<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TruckLocationUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver;
    public $coords;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($driver, $coords)
    {
        $this->driver = $driver;
        $this->coords = $coords;
    }

    public function broadcastWith()
    {
        return [
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'truck' => [
                    'number' => $this->driver->truck->number,
                ]
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
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('driver-location');
    }
}
