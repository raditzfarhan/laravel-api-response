<?php

namespace RaditzFarhan\ApiResponse;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


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
 * @method static \Illuminate\Http\JsonResponse validationError(?array $errors = null, ?string $message = null)
 * @method static \Illuminate\Http\JsonResponse tooManyRequests(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse internalServerError(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse notImplemented(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse badGateway(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse serviceUnavailable(?string $message = null)
 * @method static \Illuminate\Http\JsonResponse gatewayTimeout(?string $message = null)
 */
class ApiResponse
{
    /**
     * The payload to return as response data.
     *
     * @var array
     */
    public $payload = [];

    /**
     * The allowed attributes to be set and call as method.
     *
     * @var array
     */
    public $attributes = [
        'status',
        'http_code',
        'message',
        'data',
        'errors',
        'meta',
        'links',
        'code',
    ];

    /**
     * The canonical output order for payload keys.
     *
     * @var array
     */
    public $attributeOrder = [
        'status',
        'http_code',
        'code',
        'message',
        'data',
        'errors',
        'meta',
        'links',
    ];

    /**
     * Map of internal key names to output key names.
     *
     * @var array
     */
    private array $keyMap = [];

    /**
     * Fields to inject into every response.
     *
     * @var array
     */
    private array $globalFields = [];

    /**
     * HTTP headers to send with the response.
     *
     * @var array
     */
    private array $headers = [];

    /**
     * Set custom HTTP headers to send with the response.
     *
     * @param  array  $headers
     * @return static
     */
    public function headers(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Configure the response with package config values.
     *
     * @param  array  $config
     * @return void
     */
    public function configure(array $config): void
    {
        $this->keyMap = $config['keys'] ?? [];
        $this->globalFields = $config['global_fields'] ?? [];
    }

    /**
     * Return success json response.
     *
     * @return Illuminate\Http\Response
     *
     * @throws \BadMethodCallException
     */
    public function success()
    {
        $this->status = true;

        if (!isset($this->payload['http_code'])) {
            $this->http_code = Response::HTTP_OK;
        }

        if (!isset($this->payload['message'])) {
            $this->message = trans('laravel-api-response::messages.success') . ".";
        }

        if (!isset($this->payload['errors'])) {
            unset($this->payload['errors']);
        }

        if ($this->http_code < 200 || $this->http_code >= 300) {
            throw new BadMethodCallException(trans(
                'laravel-api-response::messages.http_code_set_failed',
                ['code' => $this->http_code]
            ));
        }

        $httpCode = $this->http_code;
        $this->reArrangePayload();

        return response()->json($this->payload, $httpCode, $this->headers);
    }

    /**
     * Return failed json response.
     *
     * @return Illuminate\Http\Response
     *
     * @throws \BadMethodCallException
     */
    public function failed()
    {
        $this->status = false;

        if (!isset($this->payload['http_code'])) {
            $this->http_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if (!isset($this->payload['message'])) {
            $this->message = trans('laravel-api-response::messages.failed') . ".";
        }

        if ($this->http_code >= 200 && $this->http_code < 300) {
            throw new BadMethodCallException(trans(
                'laravel-api-response::messages.http_code_set_success',
                ['code' => $this->http_code]
            ));
        }

        $httpCode = $this->http_code;
        $this->reArrangePayload();

        return response()->json($this->payload, $httpCode, $this->headers);
    }

    /**
     * Return create json response.
     *
     * @return Illuminate\Http\Response
     */
    public function created($data = null)
    {
        $this->http_code = Response::HTTP_CREATED;

        if ($data) {
            $this->data = $data;
        }

        if (!$this->message) {
            $this->message = trans('laravel-api-response::messages.created') . ".";
        }

        return $this->success();
    }

    /**
     * Return bad request json response.
     *
     * @return Illuminate\Http\Response
     */
    public function badRequest($message = null)
    {
        return $this->commonError(Response::HTTP_BAD_REQUEST, $message ?? $this->message ?? trans('laravel-api-response::messages.bad_request') . ".");
    }

    /**
     * Return unauthorized json response.
     *
     * @return Illuminate\Http\Response
     */
    public function unauthorized($message = null)
    {
        return $this->commonError(Response::HTTP_UNAUTHORIZED, $message ?? $this->message ?? trans('laravel-api-response::messages.unauthorized') . ".");
    }

    /**
     * Return forbidden json response.
     *
     * @return Illuminate\Http\Response
     */
    public function forbidden($message = null)
    {
        return $this->commonError(Response::HTTP_FORBIDDEN, $message ?? $this->message ?? trans('laravel-api-response::messages.forbidden') . ".");
    }

    /**
     * Return not found json response.
     *
     * @return Illuminate\Http\Response
     */
    public function notFound($message = null)
    {
        return $this->commonError(Response::HTTP_NOT_FOUND, $message ?? $this->message ?? trans('laravel-api-response::messages.not_found') . ".");
    }

    /**
     * Return validation error json response.
     *
     * @return Illuminate\Http\Response
     */
    public function validationError($errors = null, $message = null)
    {
        $this->http_code = Response::HTTP_UNPROCESSABLE_ENTITY;

        if ($errors) {
            $this->errors = $errors;
        }

        if (!$this->message) {
            $this->message = $message ?? trans('laravel-api-response::messages.validation_error') . ".";
        }

        return $this->failed();
    }

    /**
     * Return internal server error json response.
     *
     * @return Illuminate\Http\Response
     */
    public function internalServerError($message = null)
    {
        return $this->commonError(Response::HTTP_INTERNAL_SERVER_ERROR, $message ?? $this->message ?? trans('laravel-api-response::messages.internal_server_error') . ".");
    }

    /**
     * Return not implemented json response.
     *
     * @return Illuminate\Http\Response
     */
    public function notImplemented($message = null)
    {
        return $this->commonError(Response::HTTP_NOT_IMPLEMENTED, $message ?? $this->message ?? trans('laravel-api-response::messages.not_implemented') . ".");
    }

    /**
     * Return bad gateway json response.
     *
     * @return Illuminate\Http\Response
     */
    public function badGateway($message = null)
    {
        return $this->commonError(Response::HTTP_BAD_GATEWAY, $message ?? $this->message ?? trans('laravel-api-response::messages.bad_gateway') . ".");
    }

    /**
     * Return service unavailable json response.
     *
     * @return Illuminate\Http\Response
     */
    public function serviceUnavailable($message = null)
    {
        return $this->commonError(Response::HTTP_SERVICE_UNAVAILABLE, $message ?? $this->message ?? trans('laravel-api-response::messages.service_unavailable') . ".");
    }

    /**
     * Return gateway timeout json response.
     *
     * @return Illuminate\Http\Response
     */
    public function gatewayTimeout($message = null)
    {
        return $this->commonError(Response::HTTP_GATEWAY_TIMEOUT, $message ?? $this->message ?? trans('laravel-api-response::messages.gateway_timeout') . ".");
    }

    /**
     * Return method not allowed json response.
     *
     * @return Illuminate\Http\Response
     */
    public function methodNotAllowed($message = null)
    {
        return $this->commonError(Response::HTTP_METHOD_NOT_ALLOWED, $message ?? $this->message ?? trans('laravel-api-response::messages.method_not_allowed') . ".");
    }

    /**
     * Return not acceptable json response.
     *
     * @return Illuminate\Http\Response
     */
    public function notAcceptable($message = null)
    {
        return $this->commonError(Response::HTTP_NOT_ACCEPTABLE, $message ?? $this->message ?? trans('laravel-api-response::messages.not_acceptable') . ".");
    }

    /**
     * Return request timeout json response.
     *
     * @return Illuminate\Http\Response
     */
    public function requestTimeout($message = null)
    {
        return $this->commonError(Response::HTTP_REQUEST_TIMEOUT, $message ?? $this->message ?? trans('laravel-api-response::messages.request_timeout') . ".");
    }

    /**
     * Return conflict json response.
     *
     * @return Illuminate\Http\Response
     */
    public function conflict($message = null)
    {
        return $this->commonError(Response::HTTP_CONFLICT, $message ?? $this->message ?? trans('laravel-api-response::messages.conflict') . ".");
    }

    /**
     * Return gone json response.
     *
     * @return Illuminate\Http\Response
     */
    public function gone($message = null)
    {
        return $this->commonError(Response::HTTP_GONE, $message ?? $this->message ?? trans('laravel-api-response::messages.gone') . ".");
    }

    /**
     * Return too many requests json response.
     *
     * @return Illuminate\Http\Response
     */
    public function tooManyRequests($message = null)
    {
        return $this->commonError(Response::HTTP_TOO_MANY_REQUESTS, $message ?? $this->message ?? trans('laravel-api-response::messages.too_many_requests') . ".");
    }

    /**
     * Return common error json response.
     *
     * @return Illuminate\Http\Response
     */
    public function commonError($http_code, $message = null)
    {
        $this->http_code = $http_code;

        if ($message) {
            $this->message = $message;
        }

        return $this->failed();
    }

    /**
     * Return paginate json response.
     *
     * @return Illuminate\Http\Response
     */
    public function collection($data)
    {
        if (
            $data instanceof \Illuminate\Pagination\LengthAwarePaginator
            || $data instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection
        ) {
            if ($data->items()) {
                $this->data = $data->items();
            } else {
                $this->data = [];
            }

            $this->meta = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'has_more_pages' => $data->hasMorePages(),
            ];

            $this->links = [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ];
        }

        return $this->success();
    }

    /**
     * Re-arrange payload.
     *
     * @return void
     */
    private function reArrangePayload()
    {
        // 1. Order by attributeOrder
        $ordered = [];
        foreach ($this->attributeOrder as $key) {
            if (array_key_exists($key, $this->payload)) {
                $ordered[$key] = $this->payload[$key];
            }
        }
        // Preserve any keys not in attributeOrder (e.g. future additions)
        foreach ($this->payload as $key => $value) {
            if (!array_key_exists($key, $ordered)) {
                $ordered[$key] = $value;
            }
        }

        // 2. Rename keys via keyMap
        $renamed = [];
        foreach ($ordered as $key => $value) {
            $outputKey = $this->keyMap[$key] ?? $key;
            $renamed[$outputKey] = $value;
        }

        // 3. Inject global fields (after standard fields, not subject to renaming)
        foreach ($this->globalFields as $key => $value) {
            $renamed[$key] = is_callable($value) ? $value() : $value;
        }

        $this->payload = $renamed;
    }

    /**
     * Dynamically handle setter.
     *
     * @param  string  $name
     * @param  array  $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (collect($this->attributes)->contains($name)) {
            $this->payload[$name] = $value;
        }
    }

    /**
     * Dynamically handle getter.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->payload)) {
            return $this->payload[$name];
        }
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return $this|void
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        $attr_name = Str::snake($name);

        if (collect($this->attributes)->contains($attr_name)) {
            $this->$attr_name = count($arguments) > 1 ? $arguments : (isset($arguments[0]) ? $arguments[0] : $arguments);

            return $this;
        } else {
            throw new BadMethodCallException(trans(
                'laravel-api-response::messages.method_does_not_exist',
                [
                    'class' => get_class(),
                    'method' => $name
                ]
            ));
        }
    }
}
