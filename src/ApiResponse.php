<?php

namespace RaditzFarhan\ApiResponse;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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
            $this->http_code = 200;
        }

        if (!isset($this->payload['message'])) {
            $this->message = 'Success.';
        }

        if (!isset($this->payload['errors'])) {
            unset($this->payload['errors']);
        }

        if ($this->http_code < 200 || $this->http_code >= 300) {
            throw new BadMethodCallException(sprintf(
                '%s HTTP Code is set to failed instead of success.',
                $this->http_code
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
            $this->http_code = 500;
        }

        if (!isset($this->payload['message'])) {
            $this->message = 'Failed.';
        }

        if ($this->http_code >= 200 && $this->http_code < 300) {
            throw new BadMethodCallException(sprintf(
                '%s HTTP Code is set to success instead of failed.',
                $this->http_code
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
        $this->http_code = 201;

        if ($data) {
            $this->data = $data;
        }

        if (!$this->message) {
            $this->message = 'Created.';
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
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                get_class(),
                $name
            ));
        }
    }
}
