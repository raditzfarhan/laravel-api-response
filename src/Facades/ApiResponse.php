<?php

namespace RaditzFarhan\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static httpCode(int $code)
 * @method static static message(string $message)
 * @method static static data(mixed $data)
 * @method static static errors(array $errors)
 * @method static static meta(array $meta)
 * @method static static links(array $links)
 * @method static static code(int|string $code)
 * @method static static headers(array $headers)
 * @method static \Illuminate\Http\JsonResponse success()
 * @method static \Illuminate\Http\JsonResponse failed()
 * @method static \Illuminate\Http\JsonResponse created(mixed $data = null)
 * @method static \Illuminate\Http\JsonResponse collection(mixed $data)
 * @method static \Illuminate\Http\JsonResponse badRequest(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse unauthorized(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse forbidden(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse notFound(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse methodNotAllowed(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse notAcceptable(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse requestTimeout(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse conflict(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse gone(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse validationError(?array $errors = null)
 * @method static \Illuminate\Http\JsonResponse tooManyRequests(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse internalServerError(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse notImplemented(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse badGateway(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse serviceUnavailable(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse gatewayTimeout(?string $message = null)
 */
class ApiResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ApiResponse';
    }
}
