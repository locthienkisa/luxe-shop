<?php

declare(strict_types=1);

return [
    'name' => App\Core\Config::env('APP_NAME', 'Luxe Shop'),
    'env' => App\Core\Config::env('APP_ENV', 'production'),
    'debug' => App\Core\Config::env('APP_DEBUG', false),
    'url' => rtrim((string) App\Core\Config::env('APP_URL', 'http://localhost'), '/'),
    'key' => App\Core\Config::env('APP_KEY', ''),
    'timezone' => App\Core\Config::env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'session_name' => App\Core\Config::env('SESSION_NAME', 'luxe_session'),
    'session_lifetime' => (int) App\Core\Config::env('SESSION_LIFETIME', 7200),
    'shipping_fee' => (float) App\Core\Config::env('SHIPPING_FEE', 30000),
    'free_shipping_min' => (float) App\Core\Config::env('FREE_SHIPPING_MIN', 500000),
    'verified_purchase_only' => App\Core\Config::env('VERIFIED_PURCHASE_ONLY', true),
    'login_max_attempts' => (int) App\Core\Config::env('LOGIN_MAX_ATTEMPTS', 5),
    'login_lockout_minutes' => (int) App\Core\Config::env('LOGIN_LOCKOUT_MINUTES', 15),
];
