<?php

declare(strict_types=1);

use App\Controllers\Api\ApiController;
use App\Middleware\ApiAuthMiddleware;

$router->get('/api/products', [ApiController::class, 'products']);
$router->get('/api/products/{slug}', [ApiController::class, 'product']);
$router->post('/api/login', [ApiController::class, 'login']);
$router->post('/api/register', [ApiController::class, 'register']);
$router->post('/api/cart', [ApiController::class, 'cart'], [ApiAuthMiddleware::class]);
$router->post('/api/checkout', [ApiController::class, 'checkout'], [ApiAuthMiddleware::class]);
$router->get('/api/orders', [ApiController::class, 'orders'], [ApiAuthMiddleware::class]);
$router->get('/api/profile', [ApiController::class, 'profile'], [ApiAuthMiddleware::class]);
