<?php

namespace Laraquick\Tests\Traits;

use Storage;
use Illuminate\Foundation\Testing\TestResponse;
use Laraquick\Tests\State;

trait Common
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        if (!State::$migratedAfresh) {
            foreach ((config('laraquick.tests.commands.set_up.once') ?? []) as $key => $command) {
                $options = [];
                if (is_string($key)) {
                    $options = $command;
                    $command = $key;
                }
                $this->artisan($command, $options);
            }
            State::$migratedAfresh = true;
        }
        foreach ((config('laraquick.tests.commands.set_up.always') ?? []) as $key => $command) {
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

    protected function user()
    {
        if (!State::$user) {
            State::$user = factory(config('auth.providers.users.model'))
                ->create(
                    config('laraquick.tests.user', [
                        'first_name' => 'Nelseon',
                        'last_name' => 'Jones',
                        'email' => 'test2@email.com'
                    ])
                );
        }
        return State::$user;
    }

    protected function login()
    {
        return $this->actingAs($this->user());
    }

    protected function request()
    {
        $args = func_get_args();
        $method = array_shift($args);
        if (count($args) < 2 && $method !== 'get') {
            $args[] = [];
        }
        $args[] = $this->headers();
        return call_user_func_array([$this, $method], $args);
    }
    
    /**
     * Return request headers needed to interact with the API.
     *
     * @return Array array of headers.
     */
    protected function headers()
    {
        $headers = ['Accept' => 'application/json'];

        $AuthGuard = config('laraquick.tests.auth_guard');

        if ($AuthGuard && State::$user) {
            $token = call_user_func([$AuthGuard, 'fromUser'], State::$user);
            call_user_func([$AuthGuard, 'setToken'], $token);
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    protected function documentResponse(TestResponse $response, $path)
    {
        // document the response by creating a log file and streaming details to it.
        if (ends_with($path, '.json')) {
            $path = str_before($path, '.json');
        }
        $path = str_replace('.', '/', $path);
        Storage::put("docs/{$path}.json", json_encode($response->json(), JSON_PRETTY_PRINT));
    }
}
