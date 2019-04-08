<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    'proxy' => [
        'grant_type' => env('OAUTH_GRANT_TYPE'),
        'client_id' => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'scope' => env('OAUTH_SCOPE', '*'),
    ],
    'refresh_token' => [
        'grant_type' => 'refresh_token',
        'client_id' => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'scope' => env('OAUTH_SCOPE', '*'),
    ],
];
