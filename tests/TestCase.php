<?php

namespace RaditzFarhan\ApiResponse\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RaditzFarhan\ApiResponse\ApiResponseServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [ApiResponseServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ApiResponse' => \RaditzFarhan\ApiResponse\Facades\ApiResponse::class,
        ];
    }
}
