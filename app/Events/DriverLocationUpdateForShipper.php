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

class DriverLocationUpdateForShipper implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $locationGroup;
    public $driver;
    public $shipper;
    public $coords;
    public $speed;
    public $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($driver, $shipper, $locationGroup, $coords, $speed, $status)
    {
        $this->driver = $driver;
        $this->shipper = $shipper;
        $this->locationGroup = $locationGroup;
        $this->coords = $coords;
        $this->speed = $speed;
        $this->status = $status;
    }

    public function broadcastWith()
    {
        return [
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'truck' => [
                    'number' => ($this->driver->truck) ? $this->driver->truck->number : null,
                ],
                'shift' => ($this->driver->shift) ? $this->driver->shift->id : null,
            ],
            'shipper' => [
                'id' => $this->shipper->id,
                'name' => $this->shipper->name,
            ],
            'coords' => $this->coords,
            'speed' => $this->speed,
            'status' => $this->status,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('driver-location-shipper.' . $this->shipper->id);
    }
}
