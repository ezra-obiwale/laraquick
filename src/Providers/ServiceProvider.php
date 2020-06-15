<?php

namespace Laraquick\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laraquick\Commands\Database;
use Laraquick\Commands\Logs\Backup;
use Laraquick\Commands\WebSocketServer\Restart;
use Laraquick\Commands\WebSocketServer\Start;

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

        Route::macro('httpResource', function ($path, $controller, array $options = []) {
            $only = Arr::exists($options, 'only') ? $options['only'] : ['index', 'store', 'show', 'update', 'destroy'];
            $except = Arr::exists($options, 'except') ? $options['except'] : [];

            if (!is_array($only)) {
                $only = [$only];
            }

            if (!is_array($except)) {
                $except = [$except];
            }

            if (in_array('index', $only) && !in_array('index', $except)) {
                $this->get($path, $controller . '@getIndex');
            }
            if (in_array('store', $only) && !in_array('store', $except)) {
                $this->post($path, $controller . '@postStore');
            }
            if (in_array('show', $only) && !in_array('show', $except)) {
                $this->get($path . '/{id}', $controller . '@getShow');
            }
            if (in_array('update', $only) && !in_array('update', $except)) {
                $this->put($path . '/{id}', $controller . '@putUpdate');
            }
            if (in_array('destroy', $only) && !in_array('destroy', $except)) {
                $this->delete($path . '/{id}', $controller . '@deleteDestroy');
            }
        });
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

    protected function configPath(): string
    {
        return dirname(__DIR__) . "/config/laraquick.php";
    }
}
