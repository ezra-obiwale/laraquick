<?php

namespace Laraquick\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;

class WebSocket implements ShouldBroadcast
{
    use InteractsWithSockets;

    protected $channel;
    protected $eventName;
    protected $data;

    public function __construct(Channel $channel, $eventName, array $data)
    {
        $this->channel = $channel;
        $this->eventName = $eventName;
        $this->data = $data;
    }

    public function broadcastAs()
    {
        return $this->eventName;
    }

    public function broadcastOn()
    {
        return $this->channel;
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
