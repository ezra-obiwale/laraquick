<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $headers = array_merge(['Accept' => 'application/json'], Config::get('laraquick.tests.headers', []));

        $jwt = Config::get('laraquick.tests.jwt', false);

        if ($jwt && $this->user) {
            $token = JWTAuth::fromUser($this->user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }
}
