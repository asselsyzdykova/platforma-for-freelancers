<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie','freelancers', 'register'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://bezrab.space',
    'https://www.bezrab.space',
    'http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
