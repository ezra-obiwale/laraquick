<?php

namespace Laraquick\Tests\Traits;

use Storage;
use Illuminate\Foundation\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laraquick\Tests\State;
use Mockery;

trait Common
{
    protected $user;
    protected $state;

    public function setUp(): void
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
            $this->setUpOnce();
        }
        foreach ((config('laraquick.tests.commands.set_up.always', [])) as $key => $command) {
            $options = [];
            if (is_string($key)) {
                $options = $command;
                $command = $key;
            }
            $this->artisan($command, $options);
        }
        $this->setUpAlways();
    }

    protected function setUpAlways()
    {
    }

    protected function setUpOnce()
    {
    }

    protected function tearingDown()
    {
    }

    public function tearDown(): void
    {
        $this->tearingDown();

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
     * @param int $index The index of the user in the config file
     *
     * @return object
     */
    protected function user($index = 0)
    {
        if (count($this->state::$users) <= $index) {
            $users = config('laraquick.tests.users', [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'jdoe@email.com'
                ],
                [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'jane.doe@email.com'
                ]
            ]);
            if (count($users) <= $index) {
                throw new \Exception('There are only ' . count($users) . ' set up');
            }
            $this->state::$users[] = factory(config('auth.providers.users.model'))
                ->create($users[$index]);
        }
        return $this->state::$users[$index];
    }

    /**
     * Logs in the user
     *
     * @param int $userIndex The index of the user in the config file
     *
     * @return mixed
     */
    protected function login($userIndex = 0)
    {
        $this->state::$authUser = $this->user($userIndex);
        return $this->actingAs($this->state::$authUser);
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

        if ($jwt && $this->state::$authUser) {
            $token = JWTAuth::fromUser($this->state::$authUser);
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
        if (ends_with($path, '.json')) {
            $path = str_before($path, '.json');
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
     * Mocks a facade
     *
     * @param string $facadeName The name of the facade
     * @param callable $callback The function to call with the mocked class if successfully mocked
     * @return object
     */
    protected function mockFacade($facadeName, callable $callback)
    {
        $facadeClass = get_class(call_user_func([$facadeName, 'getFacadeRoot']));
        return $this->mockClass($facadeClass, $callback);
    }
}
