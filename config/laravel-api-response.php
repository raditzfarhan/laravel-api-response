<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Response Key Names
    |--------------------------------------------------------------------------
    | Rename any of the default response keys globally. Internal keys remain
    | unchanged; only the JSON output key names are substituted.
    */
    'keys' => [
        'status'    => 'status',
        'http_code' => 'http_code',
        'code'      => 'code',
        'message'   => 'message',
        'data'      => 'data',
        'errors'    => 'errors',
        'meta'      => 'meta',
        'links'     => 'links',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Fields
    |--------------------------------------------------------------------------
    | Fields automatically appended to every response. Supports static values
    | and closures for runtime values.
    |
    | Example:
    |   'version'    => '1.0',
    |   'request_id' => fn() => request()->header('X-Request-Id'),
    */
    'global_fields' => [
        //
    ],

];
