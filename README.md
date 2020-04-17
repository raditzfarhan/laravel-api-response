<p align="center"><img src="https://res.cloudinary.com/raditzfarhan/image/upload/v1587107749/laravel-api-response_c1wbwr.svg" width="640"></p>

<p align="center">   
    <a href="https://github.com/raditzfarhan/laravel-api-response"><img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square" alt="License"></a>
    <a href="https://github.com/raditzfarhan/laravel-api-response"><img src="https://github.styleci.io/repos/7548986/shield?style=square" alt="styleci"></img></a>
</p>

# Laravel API Response

Laravel and Lumen API response transformer

## Installation

Via Composer

``` bash
$ composer require raditzfarhan/laravel-api-response:^1.0
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
$app->withFacades();
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
The response will return a Illuminate\Http\Response instance just like when u call `response()` helper method.

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

// Example #2
return ApiResponse::httpCode(422)->message('Validation error!')->errors(['name' => ['Name field is required.']])->failed();
```
Above call will result in below:
```json
// Example #1
{
    "status": true,
    "http_code": 201,
    "message": "Created new record!",
    "data": {
        "name": "Raditz Farhan",
        "country": "MY"
    }    
}

// Example #2
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

## Change log

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Raditz Farhan](https://github.com/raditzfarhan)

## License

MIT. Please see the [license file](LICENSE) for more information.
