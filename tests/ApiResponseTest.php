<?php

namespace RaditzFarhan\ApiResponse\Tests;

class ApiResponseTest extends TestCase
{
    public function test_payload_keys_are_in_canonical_order()
    {
        $result = \ApiResponse::data(['foo' => 'bar'])->success();
        $keys = array_keys($result->getData(true));

        $this->assertSame(['status', 'http_code', 'message', 'data'], $keys);
    }

    public function test_each_resolution_returns_a_fresh_instance()
    {
        $instance1 = app('ApiResponse');
        $instance1->payload['leaked'] = 'value';

        $instance2 = app('ApiResponse');

        $this->assertArrayNotHasKey('leaked', $instance2->payload);
    }
}
