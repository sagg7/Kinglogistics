<?php

namespace App\Events;

use App\Models\Load;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoadUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $load;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($load)
    {
        $this->load = Load::with([
            'photos',
            'driver.carrier',
            'truck',
            'shipper',
        ])
            ->find($load->id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $carrier_id = 0;
        if ($this->load->driver)
            $carrier_id = $this->load->driver->carrier->id;
        return [
            new PrivateChannel('load-status-update-web.' . $this->load->broker_id),
            new PrivateChannel('load-status-update-carrier.' . $carrier_id),
            new PrivateChannel('load-status-update-shipper.' . $this->load->shipper->id),
        ];
    }
}
