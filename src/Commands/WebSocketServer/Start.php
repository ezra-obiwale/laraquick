<?php

namespace Laraquick\Commands\WebSocketServer;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the websocket server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $socketControllerClass = config('laraquick.websocket.controller');
        if (!class_exists($socketControllerClass)) {
            return $this->error('WebSocket controller not found');
        }
        $address = config('laraquick.websocket.allowed_ip_address');
        $port = config('laraquick.websocket.port');
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new $socketControllerClass()
                )
            ),
            intval($port),
            $address
       );
       $this->info("Websocket server running on port $port");
       $server->run();
    }
}
