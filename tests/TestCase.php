<?php

namespace Cord\Tests;

use Cord\Providers\CordServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CordServiceProvider::class,
        ];
    }
}
