<?php

namespace Laraquick\Controllers\Traits\Crud;

trait Authorize
{
    /**
     * Authorizes a method if the map for it exists. @see resourceAbilityMap()
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    protected function authorizeMethod($method, array $arguments = [])
    {
        if (!$this->usePolicy()) {
            return;
        }

        $map = $this->resourceAbilityMap();

        if (array_key_exists($method, $map)) {
            $this->authorize($map[$method], $arguments);
        }
    }

    /**
     * Indicates whether to use policy for the crud methods
     */
    protected function usePolicy(): bool
    {
        return config('laraquick.controllers.use_policies', false);
    }

    /**
     * Returns the ability map for the current resource
     */
    abstract protected function resourceAbilityMap();

    /**
     * Authorizes the action method
     */
    abstract protected function authorize($method, array $map);
}
