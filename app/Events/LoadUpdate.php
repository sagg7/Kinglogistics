<?php

namespace App\Events;

use App\Models\Load;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoadUpdate implements ShouldBroadcast
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
        $this->load = $load;
        $this->load->photos;
        $this->load->driver->carrier;
        $this->load->truck;
        $this->load->shipper;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('load-status-update');
    }
}
