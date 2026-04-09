<?php

namespace RaditzFarhan\ApiResponse\Tests;

class ApiResponseTest extends TestCase
{
    public function test_code_attribute_appears_between_http_code_and_message()
    {
        $result = \ApiResponse::code(10001)->notFound();
        $data = $result->getData(true);

        $this->assertArrayHasKey('code', $data);
        $this->assertSame(10001, $data['code']);

        $keys = array_keys($data);
        $httpCodePos = array_search('http_code', $keys);
        $codePos     = array_search('code', $keys);
        $messagePos  = array_search('message', $keys);

        $this->assertGreaterThan($httpCodePos, $codePos);
        $this->assertLessThan($messagePos, $codePos);
    }

    public function test_code_attribute_omitted_when_not_set()
    {
        $result = \ApiResponse::notFound();
        $data = $result->getData(true);

        $this->assertArrayNotHasKey('code', $data);
    }

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
