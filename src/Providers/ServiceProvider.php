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
        $this->mergeConfigFrom($this->configPath(), 'laraquick');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('laraquick.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebSocketServer::class,
            ]);
        }

    }

    protected function path()
    {
        return dirname(__DIR__) . "/config/laraquick.php";
    }

}
