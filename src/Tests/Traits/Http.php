<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use Illuminate\Support\Str;

trait Http {

    protected $user;

    /**
     * @inheritDoc
     */
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $this->asUser($user);

        return parent::actingAs($user, $driver);
    }

    /**
     * Acts as a user when using request() with jwt headers
     *
     * @param Authenticatable $uiser
     * @return self
     */
    protected function asUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Send a request with headers
     *
     * @param string $method The request method
     * @param string $url The url of the request
     * @param array $data The data for a POST/PUT request
     * @return self
     */
    protected function request($method, $url, array $data = [])
    {
        return $this->addHeaders()
            ->json($method, $url, $data);
    }

    /**
     * Adds headers to the request
     *
     * @return self
     */
    protected function addHeaders()
    {
        return $this->withHeaders($this->headers());
    }

    /**
     * Return request headers needed to interact with the API.
     *
     * @return Array array of headers.
     */
    protected function headers()
    {
        $headers = array_merge(['Accept' => 'application/json'], config('laraquick.tests.headers', []));

        $jwt = config('laraquick.tests.jwt', false);

        if ($jwt && $this->user) {
            $token = JWTAuth::fromUser($this->user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    /**
     * Save the response of a test to storage
     *
     * @param TestResponse $response
     * @param string $path
     * @param array $overrideWith An array of key=>values to override on the stored response
     * @return string
     */
    protected function storeResponse(TestResponse $response, $path, $overrideWith = [])
    {
        // document the response by creating a log file and streaming details to it.
        if (Str::endsWith($path, '.json')) {
            $path = Str::before($path, '.json');
        }

        $path = str_replace('.', '/', $path);
        $storagePath = config('laraquick.tests.storage_path', 'test-responses');
        $format = config('laraquick.tests.response_format') ?? '';

        if ($format) {
            $format = '.' . $format;
        }

        $data = collect($response->json())->merge($overrideWith)->all();

        return Storage::put("$storagePath/$path" . $format, json_encode($data, JSON_PRETTY_PRINT));
    }
}
