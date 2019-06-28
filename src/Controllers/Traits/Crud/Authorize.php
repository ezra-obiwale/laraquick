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
        if (!config('laraquick.controllers.use_policies', false)) {
            return;
        }
        if (!method_exists('resourceAbilityMap', $this)) {
            throw new \Exception('Method "resourceAbilityMap" does not exist');
        } elseif (!method_exists('authorize', $this)) {
            throw new \Exception('Method "authorize" does not exist');
        }

        $map = $this->resourceAbilityMap();
        if (array_key_exists($method, $map)) {
            $this->authorize($map[$method], $arguments);
        }
    }
}
