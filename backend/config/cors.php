<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie','freelancers', 'register'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://platforma-for-freelancers.vercel.app',
    'http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
