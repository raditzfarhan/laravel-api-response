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

    public function test_response_keys_can_be_renamed_via_config()
    {
        config(['laravel-api-response.keys.status' => 'success']);
        config(['laravel-api-response.keys.http_code' => 'code']);

        $result = \ApiResponse::success();
        $data = $result->getData(true);

        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayNotHasKey('status', $data);
        $this->assertArrayNotHasKey('http_code', $data);
    }

    public function test_global_fields_appear_in_every_response()
    {
        config(['laravel-api-response.global_fields' => ['version' => '1.0']]);

        $result = \ApiResponse::success();
        $data = $result->getData(true);

        $this->assertArrayHasKey('version', $data);
        $this->assertSame('1.0', $data['version']);
    }

    public function test_global_fields_support_closures()
    {
        config(['laravel-api-response.global_fields' => [
            'env' => fn() => 'testing',
        ]]);

        $result = \ApiResponse::success();
        $data = $result->getData(true);

        $this->assertSame('testing', $data['env']);
    }

    public function test_global_fields_appear_after_standard_fields()
    {
        config(['laravel-api-response.global_fields' => ['version' => '1.0']]);

        $result = \ApiResponse::success();
        $keys = array_keys($result->getData(true));

        $this->assertGreaterThan(array_search('message', $keys), array_search('version', $keys));
    }

    public function test_each_resolution_returns_a_fresh_instance()
    {
        $instance1 = app('ApiResponse');
        $instance1->payload['leaked'] = 'value';

        $instance2 = app('ApiResponse');

        $this->assertArrayNotHasKey('leaked', $instance2->payload);
    }
}
