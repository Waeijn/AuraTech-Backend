<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Allow CORS for all API routes
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // ALLOW ALL METHODS (GET, POST, PUT, DELETE, etc.)
    'allowed_methods' => ['*'],

    // CRITICAL FIX: Allow your React Frontend URL specifically, or '*' for all
    'allowed_origins' => ['http://localhost:3000', '*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set to true so Auth tokens/cookies can pass through
    'supports_credentials' => true,

];
