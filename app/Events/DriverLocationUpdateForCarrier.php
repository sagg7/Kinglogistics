<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdateForCarrier implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $locationGroup;
    public $driver;
    public $carrier;
    public $coords;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($driver, $carrier, $locationGroup, $coords)
    {
        $this->driver = $driver;
        $this->carrier = $carrier;
        $this->locationGroup = $locationGroup;
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
                ],
                'shift' => $this->driver->shift->id,
            ],
            'carrier' => [
                'id' => $this->carrier->id,
                'name' => $this->carrier->name,
            ],
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
        return new PrivateChannel('driver-location-carrier.' . $this->carrier->id);
    }
}
