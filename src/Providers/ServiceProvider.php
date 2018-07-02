<?php

namespace Laraquick\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laraquick\Commands\WebSocketServer;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->path('config/laraquick'), 'laraquick');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->path('config/laraquick') => config_path('laraquick.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebSocketServer::class,
            ]);
        }

    }

    protected function path($file)
    {
        return __DIR__ . "/{$file}.php";
    }

}
