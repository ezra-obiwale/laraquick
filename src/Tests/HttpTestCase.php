<?php

namespace Laraquick\Tests;

use TestCase;

if (class_exists('Tests\TestCase')) {
    class_alias('Tests\TestCase', 'TestCase');
} elseif (class_exists('App\TestCase')) {
    class_alias('App\TestCase', 'TestCase');
}

abstract class HttpTestCase extends TestCase
{
    use Traits\ApiHttpTest;
}
