<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Mockery;

trait Common
{
    /**
     * Mock a class
     *
     * @param string $className The FQN of the class
     * @param callable $callback The function to call with the mocked class if successfully mocked
     * @param boolean $static Indicate whether the intended method is static
     * @return mixed
     */
    protected function mockClass($className, callable $callback, $static = false)
    {
        $mockArgPrefix = $static ? 'alias:' : '';
        $mocked = Mockery::mock($mockArgPrefix . $className);
        $result = call_user_func($callback, $mocked);

        $this->app->instance($className, $mocked);

        return $result;
    }

    /**
     * Save the response of a test to storage
     *
     * @param TestResponse $response
     * @param string $path
     * @param array $overrideWith An array of key=>values to override on the stored response
     * @return string
     */
    protected function storeResponse(TestResponse $response, $path, $overrideWith = []): string
    {
        // document the response by creating a log file and streaming details to it.
        if (Str::endsWith($path, '.json')) {
            $path = Str::before($path, '.json');
        }

        $path = str_replace('.', '/', $path);
        $storagePath = Config::get('laraquick.tests.responses.storage_path', 'test-responses');
        $format = Config::get('laraquick.tests.responses.format', '');

        if ($format) {
            $format = '.' . $format;
        }

        $data = collect($response->json())->merge($overrideWith)->all();

        return Storage::put("$storagePath/$path" . $format, json_encode($data, JSON_PRETTY_PRINT));
    }
}
