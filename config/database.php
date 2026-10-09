<?php

declare(strict_types=1);

return [
    'host' => App\Core\Config::env('DB_HOST', '127.0.0.1'),
    'port' => App\Core\Config::env('DB_PORT', 3306),
    'database' => App\Core\Config::env('DB_DATABASE', 'luxe_shop'),
    'username' => App\Core\Config::env('DB_USERNAME', 'root'),
    'password' => App\Core\Config::env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
