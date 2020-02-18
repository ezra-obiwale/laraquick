<?php

namespace Laraquick\Commands\WebSocketServer;

use Illuminate\Console\Command;

class Restart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast websocket server restart signal';

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
        $this->laravel['cache']->forever('websocket:server:stop', true);
        $this->info('Broadcasting websocket server restart signal.');
    }
}
