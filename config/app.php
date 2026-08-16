<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Lite MVC'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'key' => env('APP_KEY', ''),
    'timezone' => 'UTC',

    'middleware' => [
        Lite\Middleware\StartSession::class,
        Lite\Middleware\VerifyCsrfToken::class,
    ],
];
