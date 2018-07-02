<?php

namespace Laraquick\Controllers;

use App\Http\Controllers\Controller;

class WebSocketController extends Controller implements SocketMessageInterface
{

    use Traits\WebSocket;

    protected function onEvent($event, $data = null)
    {
        $this->emit($event, $data);
    }

}
