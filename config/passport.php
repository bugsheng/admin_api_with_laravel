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
        'admin' => [
            'grant_type' => env('ADMIN_OAUTH_GRANT_TYPE'),
            'client_id' => env('ADMIN_OAUTH_CLIENT_ID'),
            'client_secret' => env('ADMIN_OAUTH_CLIENT_SECRET'),
            'scope' => env('ADMIN_OAUTH_SCOPE', '*'),
        ],
        'api' => [
            'grant_type' => env('API_OAUTH_GRANT_TYPE'),
            'client_id' => env('API_OAUTH_CLIENT_ID'),
            'client_secret' => env('API_OAUTH_CLIENT_SECRET'),
            'scope' => env('API_OAUTH_SCOPE', '*'),
        ],
    ],
    'refresh_token' => [
        'admin' => [
            'grant_type' => 'refresh_token',
            'client_id' => env('ADMIN_OAUTH_CLIENT_ID'),
            'client_secret' => env('ADMIN_OAUTH_CLIENT_SECRET'),
            'scope' => env('ADMIN_OAUTH_SCOPE', '*'),
        ],
        'api' => [
            'grant_type' => 'refresh_token',
            'client_id' => env('API_OAUTH_CLIENT_ID'),
            'client_secret' => env('API_OAUTH_CLIENT_SECRET'),
            'scope' => env('API_OAUTH_SCOPE', '*'),
        ],
    ],

];
