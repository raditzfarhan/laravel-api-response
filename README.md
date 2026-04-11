<p align="center"><img src="https://res.cloudinary.com/raditzfarhan/image/upload/v1587107749/laravel-api-response_c1wbwr.svg" width="640"></p>

# Laravel API Response

[![Latest Stable Version](https://poser.pugx.org/raditzfarhan/laravel-api-response/v/stable?format=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![Total Downloads](https://img.shields.io/packagist/dt/raditzfarhan/laravel-api-response?color=red&style=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![License](https://poser.pugx.org/raditzfarhan/laravel-api-response/license?format=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![StyleCI](https://github.styleci.io/repos/7548986/shield?style=square)](https://github.com/raditzfarhan/laravel-api-response)

A fluent API response formatter for Laravel. Returns consistent JSON responses across your application with minimal boilerplate.

## Requirements

- PHP ^8.0
- Laravel 9, 10, 11, 12 or 13

## Installation

```bash
composer require raditzfarhan/laravel-api-response
```

The service provider is auto-discovered. No manual registration needed.

## Basic Usage

Two styles are available — use whichever fits your preference:

```php
// Via the response() helper macro (auto-registered)
return response()->api()->success();
return response()->api()->failed();

// Via the facade
return ApiResponse::success();
return ApiResponse::failed();
```

Both return an `Illuminate\Http\JsonResponse` instance.

**Success response:**
```json
{
    "status": true,
    "http_code": 200,
    "message": "Success."
}
```

**Failed response:**
```json
{
    "status": false,
    "http_code": 500,
    "message": "Failed."
}
```

## Chaining

Build your response by chaining any combination of these methods before calling `success()` or `failed()`:

| Method | Description |
|---|---|
| `httpCode(int $code)` | Set the HTTP status code |
| `message(string $message)` | Set the response message |
| `data(mixed $data)` | Set the response data |
| `errors(array $errors)` | Set validation/error details |
| `meta(array $meta)` | Set metadata |
| `links(array $links)` | Set pagination links |
| `code(int\|string $code)` | Set an application-level error/status code |
| `headers(array $headers)` | Set custom HTTP response headers |

```php
return ApiResponse::httpCode(201)
    ->message('User created successfully.')
    ->data(['id' => 1, 'name' => 'Raditz Farhan'])
    ->success();
```

```json
{
    "status": true,
    "http_code": 201,
    "message": "User created successfully.",
    "data": {
        "id": 1,
        "name": "Raditz Farhan"
    }
}
```

## Application-Level Error Codes

Use `code()` to attach an application-specific error or status code alongside the HTTP status code:

```php
return ApiResponse::code(40401)->notFound();
```

```json
{
    "status": false,
    "http_code": 404,
    "code": 40401,
    "message": "Not found."
}
```

The `code` field only appears when set. Manage your own code definitions using constants or enums in your application.

## Custom HTTP Headers

Use `headers()` to attach custom HTTP response headers. Headers are sent with the response but never appear in the JSON body:

```php
return ApiResponse::headers([
    'X-Request-Id' => (string) Str::uuid(),
    'X-Version'    => '1.0',
])->success();
```

## Shorthand Methods

Common HTTP responses have dedicated shorthand methods. All accept an optional `$message` parameter:

```php
// 2xx
return ApiResponse::created($data);         // 201
return ApiResponse::collection($paginator); // 200 with meta & links

// 4xx
return ApiResponse::badRequest();           // 400
return ApiResponse::unauthorized();         // 401
return ApiResponse::forbidden();            // 403
return ApiResponse::notFound();             // 404
return ApiResponse::methodNotAllowed();     // 405
return ApiResponse::notAcceptable();        // 406
return ApiResponse::requestTimeout();       // 408
return ApiResponse::conflict();             // 409
return ApiResponse::gone();                 // 410
return ApiResponse::validationError();      // 422
return ApiResponse::tooManyRequests();      // 429

// 5xx
return ApiResponse::internalServerError();  // 500
return ApiResponse::notImplemented();       // 501
return ApiResponse::badGateway();           // 502
return ApiResponse::serviceUnavailable();   // 503
return ApiResponse::gatewayTimeout();       // 504
```

Pass a custom message to any of them:

```php
return ApiResponse::conflict('A record with this email already exists.');
```

### Validation Errors

```php
return ApiResponse::validationError($validator->errors()->toArray());
```

```json
{
    "status": false,
    "http_code": 422,
    "message": "Validation error.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Paginated Collections

Pass a `LengthAwarePaginator` or `AnonymousResourceCollection` to `collection()`:

```php
return ApiResponse::collection(Post::paginate(25));
```

```json
{
    "status": true,
    "http_code": 200,
    "message": "Success.",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "from": 1,
        "to": 25,
        "per_page": 25,
        "total": 60,
        "has_more_pages": true
    },
    "links": {
        "first": "https://example.com/posts?page=1",
        "last": "https://example.com/posts?page=3",
        "prev": null,
        "next": "https://example.com/posts?page=2"
    }
}
```

## Exception Handling

To ensure all API error responses — including Laravel's built-in exceptions — follow the same structure, register custom renderers in your exception handler.

**Laravel 11+ (`bootstrap/app.php`):**

```php
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use RaditzFarhan\ApiResponse\Facades\ApiResponse;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (ValidationException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        }
    });

    $exceptions->render(function (AuthenticationException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::unauthorized();
        }
    });

    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::notFound();
        }
    });
})
```

**Laravel 9 / 10 (`app/Exceptions/Handler.php`):**

```php
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use RaditzFarhan\ApiResponse\Facades\ApiResponse;

public function register(): void
{
    $this->renderable(function (ValidationException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        }
    });

    $this->renderable(function (AuthenticationException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::unauthorized();
        }
    });

    $this->renderable(function (NotFoundHttpException $e, Request $request) {
        if ($request->expectsJson()) {
            return ApiResponse::notFound();
        }
    });
}
```

The `$request->expectsJson()` guard ensures non-API routes (web, Blade) are unaffected and still render the default HTML error pages.

## Configuration

Publish the config file to customise key names and add global fields:

```bash
php artisan vendor:publish --provider="RaditzFarhan\ApiResponse\ApiResponseServiceProvider"
```

This creates `config/laravel-api-response.php`.

### Rename Response Keys

Rename any of the default JSON keys globally without changing your application code:

```php
'keys' => [
    'status'    => 'success',   // "status" → "success"
    'http_code' => 'code',      // "http_code" → "code"
    'message'   => 'message',
    'data'      => 'data',
    'errors'    => 'errors',
    'meta'      => 'meta',
    'links'     => 'links',
    'code'      => 'error_code',
],
```

### Global Fields

Append fields to every response automatically. Supports static values and closures:

```php
'global_fields' => [
    'version'    => '1.0',
    'request_id' => fn() => request()->header('X-Request-Id'),
],
```

Every response will then include:

```json
{
    "status": true,
    "http_code": 200,
    "message": "Success.",
    "version": "1.0",
    "request_id": "abc-123"
}
```

## IDE Support

All chainable and shorthand methods are annotated with `@method` PHPDoc on both the `ApiResponse` class and the facade, giving full autocomplete in PhpStorm, VS Code, and other IDEs.

## Change log

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Raditz Farhan](https://github.com/raditzfarhan)

## License

MIT. Please see the [license file](LICENSE) for more information.
