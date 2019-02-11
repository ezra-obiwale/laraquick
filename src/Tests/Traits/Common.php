<?php

namespace Laraquick\Tests\Traits;

use Storage;
use Illuminate\Foundation\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laraquick\Tests\State;

trait Common
{
    protected $user;
    protected $state;

    public function setUp()
    {
        parent::setUp();

        $this->state = config('laraquick.tests.classes.state', State::class);

        if (!$this->state::$migratedAfresh) {
            foreach ((config('laraquick.tests.commands.set_up.once', [])) as $key => $command) {
                $options = [];
                if (is_string($key)) {
                    $options = $command;
                    $command = $key;
                }
                $this->artisan($command, $options);
            }
            $this->state::$migratedAfresh = true;
        }
        foreach ((config('laraquick.tests.commands.set_up.always', [])) as $key => $command) {
            $options = [];
            if (is_string($key)) {
                $options = $command;
                $command = $key;
            }
            $this->artisan($command, $options);
        }
    }

    public function tearDown()
    {
        foreach ((config('laraquick.tests.commands.tear_down', [])) as $key => $command) {
            $options = [];
            if (is_string($key)) {
                $options = $command;
                $command = $key;
            }
            $this->artisan($command, $options);
        }

        parent::tearDown();
    }

    /**
     * Creates a user and return the instance
     *
     * @return object
     */
    protected function user()
    {
        if (!$this->state::$user) {
            $this->state::$user = factory(config('auth.providers.users.model'))
                ->create(
                    config('laraquick.tests.user_info', [
                        'first_name' => 'Nelseon',
                        'last_name' => 'Jones',
                        'email' => 'test2@email.com'
                    ])
                );
        }
        return $this->state::$user;
    }

    /**
     * Logs in the user
     *
     * @return mixed
     */
    protected function login()
    {
        return $this->actingAs($this->user());
    }

    /**
     * Send a request with headers
     *
     * @param string $method The request method
     * @param string $url The url of the request
     * @param array $data The data for a POST/PUT request
     * @return mixed
     */
    protected function request($method, $url, array $data = [])
    {
        return $this->withHeaders($this->headers())
            ->json($method, $url, $data);
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

        if ($jwt && $this->state::$user) {
            $token = JWTAuth::fromUser($this->state::$user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    /**
     * Save the response of a test to storage
     *
     * @param TestResponse $response
     * @param strng $path
     * @return string
     */
    protected function storeResponse(TestResponse $response, $path)
    {
        // document the response by creating a log file and streaming details to it.
        if (ends_with($path, '.json')) {
            $path = str_before($path, '.json');
        }
        $path = str_replace('.', '/', $path);
        $storagePath = config('laraquick.tests.storage_path', 'test-responses');
        return Storage::put("$storagePath/$path.json", json_encode($response->json(), JSON_PRETTY_PRINT));
    }
}
