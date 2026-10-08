<?php

declare(strict_types=1);

use App\Controllers\Admin\BannerAdminController;
use App\Controllers\Admin\CategoryAdminController;
use App\Controllers\Admin\CouponAdminController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\InventoryAdminController;
use App\Controllers\Admin\OrderAdminController;
use App\Controllers\Admin\ProductAdminController;
use App\Controllers\Admin\ReviewAdminController;
use App\Controllers\Admin\SettingAdminController;
use App\Controllers\Admin\UserAdminController;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\SuperAdminMiddleware;

$admin = [AdminMiddleware::class];
$adminCsrf = [AdminMiddleware::class, CsrfMiddleware::class];
$super = [AdminMiddleware::class, SuperAdminMiddleware::class, CsrfMiddleware::class];

$router->get('/admin', [DashboardController::class, 'index'], $admin);

$router->get('/admin/san-pham', [ProductAdminController::class, 'index'], $admin);
$router->get('/admin/san-pham/them', [ProductAdminController::class, 'create'], $admin);
$router->post('/admin/san-pham', [ProductAdminController::class, 'store'], $adminCsrf);
$router->get('/admin/san-pham/{id}/sua', [ProductAdminController::class, 'edit'], $admin);
$router->post('/admin/san-pham/{id}', [ProductAdminController::class, 'update'], $adminCsrf);
$router->post('/admin/san-pham/xoa', [ProductAdminController::class, 'delete'], $adminCsrf);

$router->get('/admin/danh-muc', [CategoryAdminController::class, 'index'], $admin);
$router->post('/admin/danh-muc', [CategoryAdminController::class, 'store'], $adminCsrf);
$router->post('/admin/danh-muc/{id}', [CategoryAdminController::class, 'update'], $adminCsrf);
$router->post('/admin/danh-muc/xoa', [CategoryAdminController::class, 'delete'], $adminCsrf);

$router->get('/admin/don-hang', [OrderAdminController::class, 'index'], $admin);
$router->get('/admin/don-hang/{id}', [OrderAdminController::class, 'show'], $admin);
$router->post('/admin/don-hang/trang-thai', [OrderAdminController::class, 'status'], $adminCsrf);
$router->post('/admin/don-hang/thanh-toan', [OrderAdminController::class, 'paid'], $adminCsrf);

$router->get('/admin/nguoi-dung', [UserAdminController::class, 'index'], $admin);
$router->get('/admin/nguoi-dung/{id}', [UserAdminController::class, 'show'], $admin);
$router->post('/admin/nguoi-dung/khoa', [UserAdminController::class, 'lock'], $adminCsrf);
$router->post('/admin/nguoi-dung/vai-tro', [UserAdminController::class, 'role'], $super);

$router->get('/admin/voucher', [CouponAdminController::class, 'index'], $admin);
$router->post('/admin/voucher', [CouponAdminController::class, 'store'], $adminCsrf);
$router->post('/admin/voucher/{id}', [CouponAdminController::class, 'update'], $adminCsrf);
$router->post('/admin/voucher/xoa', [CouponAdminController::class, 'delete'], $adminCsrf);

$router->get('/admin/danh-gia', [ReviewAdminController::class, 'index'], $admin);
$router->post('/admin/danh-gia/duyet', [ReviewAdminController::class, 'moderate'], $adminCsrf);
$router->post('/admin/danh-gia/xoa', [ReviewAdminController::class, 'delete'], $adminCsrf);

$router->get('/admin/banner', [BannerAdminController::class, 'index'], $admin);
$router->post('/admin/banner', [BannerAdminController::class, 'store'], $adminCsrf);
$router->post('/admin/banner/xoa', [BannerAdminController::class, 'delete'], $adminCsrf);

$router->get('/admin/ton-kho', [InventoryAdminController::class, 'index'], $admin);
$router->post('/admin/ton-kho', [InventoryAdminController::class, 'adjust'], $adminCsrf);

$router->get('/admin/cai-dat', [SettingAdminController::class, 'index'], $admin);
$router->post('/admin/cai-dat', [SettingAdminController::class, 'save'], $adminCsrf);
