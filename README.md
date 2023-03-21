<p align="center"><img src="https://res.cloudinary.com/raditzfarhan/image/upload/v1587107749/laravel-api-response_c1wbwr.svg" width="640"></p>

# Laravel API Response

[![Latest Stable Version](https://poser.pugx.org/raditzfarhan/laravel-api-response/v/stable?format=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![Total Downloads](https://img.shields.io/packagist/dt/raditzfarhan/laravel-api-response?color=red&style=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![License](https://poser.pugx.org/raditzfarhan/laravel-api-response/license?format=flat-square)](https://packagist.org/packages/raditzfarhan/laravel-api-response)
[![StyleCI](https://github.styleci.io/repos/7548986/shield?style=square)](https://github.com/raditzfarhan/laravel-api-response)

Laravel and Lumen API response transformer/formatter.

## Requirements
- PHP ^7.4 | ^8.0
- Laravel 7, 8, 9 or 10

## Installation

Via Composer

``` bash
$ composer require raditzfarhan/laravel-api-response
```

## Configuration

The Laravel and Lumen configurations vary slightly, so here are the instructions for each of the frameworks.

### Laravel

Edit the `config/app.php` file and add the following line to register the service provider:

```php
'providers' => [
    ...
    RaditzFarhan\ApiResponse\ApiResponseServiceProvider::class,
    ...
],
```

> Tip: If you're on Laravel version **5.5** or higher, you can skip this part of the setup in favour of the Auto-Discovery feature.

### Lumen

Edit the `bootstrap/app.php` file and add the following line to register the service provider:

```php
...
$app->register(RaditzFarhan\ApiResponse\ApiResponseServiceProvider::class);
...
```

You will also need to enable `Facades`  in `bootstrap/app.php`:

```php
..
$app->withFacades(true, [
    RaditzFarhan\ApiResponse\Facades\ApiResponse::class => 'ApiResponse'
]);
...
```

## Usage

Example usage as below snippet:

```php
// Success response

// using service container
$response = app('ApiResponse')->success();

// using alias
$response = \ApiResponse::success();

// Failed response
$response = \ApiResponse::failed();

```
The response will return a `Illuminate\Http\Response` instance just like when u call `response()` helper method.

> By default, success will use http **200** code if not set, and failed will use http **500** code if not set.

Typical response content as follow:
```json
// success
{
    "status": true,
    "http_code": 200,
    "message": "Success."
}

// failed
{
    "status": false,
    "http_code": 500,
    "message": "Failed."
}
```
Add/Change payload data by chaining more methods as below:
```php
// Example #1
return ApiResponse::httpCode(201)->message('Created new record!')->data(['name' => 'Raditz Farhan', 'country' => 'MY'])->success();

// or can be shorten to
return ApiResponse::created(['name' => 'Raditz Farhan', 'country' => 'MY']);

// Example #2
return ApiResponse::httpCode(422)->message('Validation error!')->errors(['name' => ['Name field is required.']])->failed();

// or can be shorten to
return ApiResponse::validationError(['name' => ['Name field is required.']]);
```
Above call will result in below:
Example #1
```json
{
    "status": true,
    "http_code": 201,
    "message": "Created new record!",
    "data": {
        "name": "Raditz Farhan",
        "country": "MY"
    }    
}
```
Example #2
```json

{
    "status": false,
    "http_code": 422,
    "message": "Validation error!",
    "errors": {
        "name": [
            "Name field is required."
        ]
    },
}
```

Use `collection` method to return paginate result that includes `meta` and `links` attribute:

```php
return ApiResponse::collection(App\Post::paginate());
```

Will return below result:

```json
{
  "status": true,
  "http_code": 200,
  "message": "Success.",
  "data": [
    {
      "id": 1,
      "title": "First post",
      "slug": "first-post",
      "content": "This is the first post",
      "sort_order": 1,
      "created_at": "2020-04-21T13:40:45.000000Z",
      "updated_at": "2020-04-21T13:40:45.000000Z"
    },
    ...
  ],
  "meta": {
    "currenct_page": 1,
    "last_page": 3,
    "from": 1,
    "to": 25,
    "per_page": 25,
    "total": 60,
    "has_more_pages": true
  },
  "links": {
    "first": "http://your-app-url?page=1",
    "last": "http://your-app-url?page=3",
    "prev": null,
    "next": "http://your-app-url?page=2"
  }
}
```

Besides `created` and `validationError`, below shorthand methods are available for your convenience:
```php
// return http 400 Bad request error.
return ApiResponse::badRequest('Optional message here'); 

// return http 401 Unauthorized error.
return ApiResponse::unauthorized(); 

// return http 403 Forbidden error.
return ApiResponse::forbidden(); 

// return http 404 Not found error.
return ApiResponse::notFound(); 

// return http 500 Internal server error.
return ApiResponse::internalServerError(); 
```
> Tip: Pass a message to the method to put your own custom message.

## Change log

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Raditz Farhan](https://github.com/raditzfarhan)

## License

MIT. Please see the [license file](LICENSE) for more information.
