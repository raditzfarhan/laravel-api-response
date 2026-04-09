<?php

namespace RaditzFarhan\ApiResponse\Tests;

class ApiResponseTest extends TestCase
{
    public function test_response_macro_is_registered()
    {
        $result = response()->api()->success();

        $this->assertSame(200, $result->getStatusCode());
        $this->assertTrue($result->getData(true)['status']);
    }

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

    public function test_custom_headers_are_sent_with_response()
    {
        $result = \ApiResponse::headers(['X-Request-Id' => 'abc123'])->success();

        $this->assertSame('abc123', $result->headers->get('X-Request-Id'));
    }

    public function test_headers_do_not_appear_in_json_payload()
    {
        $result = \ApiResponse::headers(['X-Request-Id' => 'abc123'])->success();
        $data = $result->getData(true);

        $this->assertArrayNotHasKey('headers', $data);
        $this->assertArrayNotHasKey('X-Request-Id', $data);
    }

    public function test_headers_work_with_failed_response()
    {
        $result = \ApiResponse::headers(['X-Custom' => 'value'])->failed();

        $this->assertSame('value', $result->headers->get('X-Custom'));
    }

    /** @dataProvider shorthandMethodProvider */
    public function test_shorthand_methods_return_correct_http_code(string $method, int $expectedCode)
    {
        $result = \ApiResponse::$method();
        $this->assertSame($expectedCode, $result->getStatusCode());
        $this->assertSame($expectedCode, $result->getData(true)['http_code']);
        $this->assertFalse($result->getData(true)['status']);
    }

    public static function shorthandMethodProvider(): array
    {
        return [
            'methodNotAllowed'  => ['methodNotAllowed', 405],
            'notAcceptable'     => ['notAcceptable', 406],
            'requestTimeout'    => ['requestTimeout', 408],
            'conflict'          => ['conflict', 409],
            'gone'              => ['gone', 410],
            'tooManyRequests'   => ['tooManyRequests', 429],
        ];
    }

    public function test_shorthand_methods_accept_custom_message()
    {
        $result = \ApiResponse::conflict('Resource already exists.');
        $this->assertSame('Resource already exists.', $result->getData(true)['message']);
    }

    public function test_each_resolution_returns_a_fresh_instance()
    {
        $instance1 = app('ApiResponse');
        $instance1->payload['leaked'] = 'value';

        $instance2 = app('ApiResponse');

        $this->assertArrayNotHasKey('leaked', $instance2->payload);
    }
}
