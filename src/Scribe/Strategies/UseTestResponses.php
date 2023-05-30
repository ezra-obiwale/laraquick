<?php

namespace Laraquick\Scribe\Strategies;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

class UseTestResponses extends Strategy
{
    /**
     * Trait containing some helper methods for dealing with "parameters",
     * such as generating examples and casting values to types.
     * Useful if your strategy extracts information about parameters or generates examples.
     */
    use ParamHelpers;

    /**
     * @link https://scribe.knuckles.wtf/laravel/advanced/plugins
     * @param ExtractedEndpointData $endpointData The endpoint we are currently processing.
     *   Contains details about httpMethods, controller, method, route, url, etc, as well as already extracted data.
     * @param array $routeRules Array of rules for the ruleset which this route belongs to.
     *
     * See the documentation linked above for more details about writing custom strategies.
     *
     * @return array|null
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): ?array
    {
        if ($endpointData->responses->hasSuccessResponse()) {
            return [];
        }

        $responsesPath = config('laraquick.tests.responses.storage_path');

        $controller = basename(str_replace('\\', '/', $endpointData->controller->name));
        $controller = Str::beforeLast($controller, 'Controller');
        $controller = Str::kebab($controller);

        $path = $responsesPath . '/' . $controller . '/' . Str::kebab($endpointData->method->name);

        if ($format = config('laraquick.tests.responses.format')) {
            $path .= '.' . $format;
        }

        if (!Storage::exists($path)) {
            return [];
        }

        $files = Storage::allFiles($path);

        $responses = [];

        foreach ($files as $file) {
            $responses[] = [
                'status' => basename($file),
                'content' => Storage::get($file)
            ];
        }

        return $responses;
    }
}
