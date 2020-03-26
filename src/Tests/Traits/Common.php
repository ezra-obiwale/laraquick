<?php

namespace Laraquick\Tests\Traits;

use Mockery;

trait Common
{
    use Http;

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
}
