<?php

namespace RaditzFarhan\ApiResponse\Tests;

class ApiResponseTest extends TestCase
{
    public function test_each_resolution_returns_a_fresh_instance()
    {
        $instance1 = app('ApiResponse');
        $instance1->payload['leaked'] = 'value';

        $instance2 = app('ApiResponse');

        $this->assertArrayNotHasKey('leaked', $instance2->payload);
    }
}
