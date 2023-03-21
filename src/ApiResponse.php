<?php

namespace RaditzFarhan\ApiResponse;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


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
    ];

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

        $this->reArrangePayload();

        return response()->json($this->payload, $this->http_code);
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

        $this->reArrangePayload();

        return response()->json($this->payload, $this->http_code);
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
    public function validationError($errors = null)
    {
        $this->http_code = Response::HTTP_UNPROCESSABLE_ENTITY;

        if ($errors) {
            $this->errors = $errors;
        }

        if (!$this->message) {
            $this->message = trans('laravel-api-response::messages.validation_error') . ".";
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
        $attributes = $this->attributes;
        krsort($attributes);

        foreach ($attributes as $attr) {
            if (isset($this->payload[$attr])) {
                $this->payload = Arr::prepend($this->payload, $this->$attr, $attr);
            }
        }
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
