<?php

namespace Laraquick\Docs\Scribe\Strategies;

use Illuminate\Support\Str;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

class EnhanceMetadata extends Strategy
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
        $controller = basename(str_replace('\\', '/', $endpointData->controller->name));
        $controller = Str::beforeLast($controller, 'Controller');
        $controller = str_replace('-', ' ', Str::kebab($controller));

        return [
            'description' => str_replace(
                ['items', 'item'],
                [Str::plural($controller), $controller],
                $endpointData->metadata->description
            )
        ];
    }

}
