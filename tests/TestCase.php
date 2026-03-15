<?php

namespace Tests;

use Cord\Providers\CordServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CordServiceProvider::class,
        ];
    }
}
