<?php

namespace Laraquick\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
            $only = $options['only'] ?? ['index', 'store', 'show', 'update', 'destroy'];
            $except = $options['except'] ?? [];
            $namePrefix = $options['namePrefix'] ?? preg_replace('[^a-zA-Z0-9]', '.', $path);
            $actionNames = ($options['actionNames'] ?? []) + [
                'index' => 'index',
                'store' => 'store',
                'show' => 'show',
                'update' => 'update',
                'destroy' => 'destroy',
            ];

            if (!is_array($only)) {
                $only = [$only];
            }

            if (!is_array($except)) {
                $except = [$except];
            }

            if (in_array('index', $only) && !in_array('index', $except)) {
                $this->get($path, $controller . '@' . $actionNames['index'])->name("{$namePrefix}.index");
            }
            if (in_array('store', $only) && !in_array('store', $except)) {
                $this->post($path, $controller . '@' . $actionNames['store'])->name("{$namePrefix}.store");
            }
            if (in_array('show', $only) && !in_array('show', $except)) {
                $this->get($path . '/{id}', $controller . '@' . $actionNames['show'])->name("{$namePrefix}.show");
            }
            if (in_array('update', $only) && !in_array('update', $except)) {
                $this->put($path . '/{id}', $controller . '@' . $actionNames['update'])->name("{$namePrefix}.update");
            }
            if (in_array('destroy', $only) && !in_array('destroy', $except)) {
                $this->delete($path . '/{id}', $controller . '@' . $actionNames['destroy'])->name("{$namePrefix}.destroy");
            }

            return $this;
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
        ], 'laraquick-config');
    }

    protected function configPath(): string
    {
        return dirname(__DIR__) . "/config/laraquick.php";
    }
}
