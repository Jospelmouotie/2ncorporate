<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'register'],

    'allowed_methods' => ['*'],

    // Remplace par ton URL exacte si tu n'utilises pas localhost
    'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // TRÈS IMPORTANT : Doit être à true pour autoriser les sessions/cookies
    'supports_credentials' => true,
];
