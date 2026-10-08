<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\ReviewController;
use App\Controllers\WishlistController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;

$csrf = [CsrfMiddleware::class];
$auth = [AuthMiddleware::class];
$authCsrf = [AuthMiddleware::class, CsrfMiddleware::class];
$guest = [GuestMiddleware::class];

$router->get('/', [HomeController::class, 'index']);
$router->get('/sitemap.xml', [HomeController::class, 'sitemap']);
$router->get('/san-pham', [ProductController::class, 'index']);
$router->get('/san-pham/{slug}', [ProductController::class, 'show']);
$router->get('/danh-muc/{slug}', [ProductController::class, 'category']);

$router->get('/gio-hang', [CartController::class, 'index']);
$router->post('/gio-hang/them', [CartController::class, 'add'], $csrf);
$router->post('/gio-hang/cap-nhat', [CartController::class, 'update'], $csrf);
$router->post('/gio-hang/xoa', [CartController::class, 'remove'], $csrf);
$router->post('/gio-hang/xoa-tat', [CartController::class, 'clear'], $csrf);
$router->post('/gio-hang/coupon', [CartController::class, 'coupon'], $csrf);

$router->get('/dang-nhap', [AuthController::class, 'showLogin'], $guest);
$router->post('/dang-nhap', [AuthController::class, 'login'], array_merge($guest, $csrf));
$router->get('/dang-ky', [AuthController::class, 'showRegister'], $guest);
$router->post('/dang-ky', [AuthController::class, 'register'], array_merge($guest, $csrf));
$router->post('/dang-xuat', [AuthController::class, 'logout'], $csrf);
$router->get('/quen-mat-khau', [AuthController::class, 'showForgot'], $guest);
$router->post('/quen-mat-khau', [AuthController::class, 'forgot'], array_merge($guest, $csrf));
$router->get('/dat-lai-mat-khau', [AuthController::class, 'showReset'], $guest);
$router->post('/dat-lai-mat-khau', [AuthController::class, 'reset'], array_merge($guest, $csrf));

$router->get('/tai-khoan', [AccountController::class, 'index'], $auth);
$router->post('/tai-khoan', [AccountController::class, 'update'], $authCsrf);
$router->get('/tai-khoan/doi-mat-khau', [AccountController::class, 'showPassword'], $auth);
$router->post('/tai-khoan/doi-mat-khau', [AccountController::class, 'password'], $authCsrf);
$router->get('/tai-khoan/dia-chi', [AccountController::class, 'addresses'], $auth);
$router->post('/tai-khoan/dia-chi', [AccountController::class, 'storeAddress'], $authCsrf);
$router->post('/tai-khoan/dia-chi/xoa', [AccountController::class, 'deleteAddress'], $authCsrf);
$router->get('/thong-bao', [AccountController::class, 'notifications'], $auth);
$router->post('/thong-bao/doc', [AccountController::class, 'readNotification'], $authCsrf);
$router->post('/thong-bao/doc-tat', [AccountController::class, 'readAll'], $authCsrf);

$router->get('/yeu-thich', [WishlistController::class, 'index'], $auth);
$router->post('/yeu-thich', [WishlistController::class, 'toggle'], $authCsrf);
$router->post('/yeu-thich/xoa', [WishlistController::class, 'remove'], $authCsrf);

$router->get('/thanh-toan', [CheckoutController::class, 'index'], $auth);
$router->post('/thanh-toan', [CheckoutController::class, 'place'], $authCsrf);

$router->get('/don-hang', [OrderController::class, 'index'], $auth);
$router->get('/don-hang/{id}', [OrderController::class, 'show'], $auth);
$router->post('/don-hang/huy', [OrderController::class, 'cancel'], $authCsrf);

$router->post('/danh-gia', [ReviewController::class, 'store'], $authCsrf);
$router->post('/danh-gia/sua', [ReviewController::class, 'update'], $authCsrf);
$router->post('/danh-gia/xoa', [ReviewController::class, 'delete'], $authCsrf);
