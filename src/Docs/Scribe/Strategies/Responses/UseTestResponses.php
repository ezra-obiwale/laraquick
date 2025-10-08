<?php

namespace Laraquick\Docs\Scribe\Strategies\Responses;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Knuckles\Scribe\Tools\Utils;

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
        $docs = [];

        $responseFiles = $this->getTestResponseFiles($endpointData);

        foreach ($responseFiles as $responseFile) {
            $docs[] = [
                'status' => (int) basename($responseFile),
                'content' => Storage::get($responseFile),
            ];
        }

        return $docs;
    }

    private function getTestResponseFiles(ExtractedEndpointData $endpointData): array
    {
        if (config('laraquick.tests.responses.format') !== 'json') {
            return [];
        }

        $testResponsesPath = config('laraquick.tests.responses.storage_path');

        $path = $this->getPathFromResponsePaths($endpointData) ?? $this->generatePath($endpointData);
        $fullPath = $testResponsesPath . DIRECTORY_SEPARATOR . ($path ?? '');

        if (is_null($path) || !Storage::exists($fullPath)) {
            return [];
        }

        return Storage::allFiles($fullPath);
    }

    private function getPathFromResponsePaths(ExtractedEndpointData $endpointData): ?string
    {
        if (!$endpointData->controller->hasMethod('responsePaths')) {
            return null;
        }

        $paths = $endpointData->controller->newInstanceWithoutConstructor()->responsePaths();

        if (is_array($paths)) {
            return $paths[$endpointData->method->getName()] ?? null;
        }

        return null;
    }

    private function generatePath(ExtractedEndpointData $endpointData): string
    {
        [$controllerName, $methodName] = Utils::getRouteClassAndMethodNames($endpointData->route);

        if (!is_string($controllerName)) {
            return [];
        }

        $controllerResourceName = $this->getControllerResourceName($controllerName);

        return $controllerResourceName . DIRECTORY_SEPARATOR . Str::kebab($methodName);
    }

    private function getControllerResourceName(string $controllerName): string
    {
        return Str::kebab(
            Str::before(
                basename(
                    str_replace('\\', DIRECTORY_SEPARATOR, $controllerName)
                ),
                'Controller'
            )
        );
    }
}
