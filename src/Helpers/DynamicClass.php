<?php

namespace Laraquick\Helpers;

class DynamicClass
{
    private $properties;
    private $methods;
    private $isLocked = false;

    /**
     * Adds a method to the class
     *
     * @param string $name The method name
     * @param mixed $returnValue The value to return when the method is called. If a function, it would receive the provided arguments.
     * @return self
     */
    public function addMethod($name, $returnValue = null)
    {
        if (!$this->isLocked) {
            $this->methods[$name] = $returnValue;
        }
        return $this;
    }

    /**
     * Adds a property to the class
     *
     * @param string $name The property name
     * @param mixed $value The value to return when the property is fetched
     * @return self
     */
    public function addProperty($name, $value = null)
    {
        if (!$this->isLocked) {
            $this->properties[$name] = $value;
        }
        return $this;
    }

    /**
     * Change the lock state of the class.
     *
     * @param boolean $status
     * @return self
     */
    public function lock($status = true)
    {
        $this->isLocked = $status;
        return $this;
    }

    /**
     * Fetches the lock status of the class
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    public function __call($method, $args)
    {
        if ($value = @$this->methods[$method]) {
            if (is_callable($value)) {
                return call_user_func_array($value, $args);
            }
            return $value;
        }
    }

    public function __get($name)
    {
        return @$this->properties[$name];
    }
}
