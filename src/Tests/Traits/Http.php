<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;

trait Http {

    protected Authenticatable $user;

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
    protected function asUser(Authenticatable $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Adds headers to the request
     *
     * @return self
     */
    protected function addHeaders(): self
    {
        return $this->withHeaders($this->headers());
    }

    /**
     * Return request headers needed to interact with the API.
     *
     * @return array Array of headers.
     */
    protected function headers(): array
    {
        return array_merge(['Accept' => 'application/json'], Config::get('laraquick.tests.headers', []));
    }
}
