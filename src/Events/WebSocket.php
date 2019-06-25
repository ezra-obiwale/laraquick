<?php

namespace Laraquick\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class WebSocket implements ShouldBroadcast
{
    protected $event;
    protected $data;
    
    public function __construct(Channel $event, array $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return $this->event;
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
