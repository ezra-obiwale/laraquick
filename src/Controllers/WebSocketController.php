<?php

namespace Laraquick\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

class WebSocketController extends Controller implements SocketMessageInterface
{
    use Traits\WebSocket;

    protected function onEvent($event, $data, $client)
    {
        $routeEvent = strtoupper($event);

        if (in_array($routeEvent, ['GET', 'POST', 'PUT', 'DELETE'])) {
            $this->handleRoute($routeEvent, $data, $client);
        }

        $this->emit($event, $data);
    }

    protected function handleRoute($method, $routeData, $client)
    {
        // path, payload, query
        extract($routeData);

        if (!$path) {
            return $this->emit('routeError', [
                'message' => $this->translate('Path not specified'),
                'method' => $method,
                'data' => $routeData
            ], $client);
        }

        $request = new Request($payload ?? []);

        $server = $request->server();
        $server['REQUEST_METHOD'] = $method;
        $server['REQUEST_URI'] = $path;

        $request->query($payload ?? []);

        $kernel = app()->make(Kernel::class);
        $subRes = $kernel->handle($request);
        $subRes->send();
    }
}
