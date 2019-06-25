<?php

namespace Laraquick\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laraquick\Commands\Database;
use Laraquick\Commands\Logs\Backup;
use Laraquick\Commands\WebSocketServer\Restart;
use Laraquick\Commands\WebSocketServer\Start;
use Illuminate\Http\Request;

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
        Request::macro();
        
        $this->publishes([
            $this->configPath() => config_path('laraquick.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Backup::class,
                Database::class,
                Start::class,
                Restart::class,
            ]);
        }
    }

    protected function configPath()
    {
        return dirname(__DIR__) . "/config/laraquick.php";
    }
}
