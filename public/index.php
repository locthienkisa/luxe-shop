<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

require dirname(__DIR__) . '/app/Helpers/functions.php';

use App\Core\Config;
use App\Core\Session;
use App\Core\Csrf;
use App\Core\Auth;
use App\Core\View;
use App\Core\Router;
use App\Core\Request;
use App\Core\Logger;
use App\Core\Database;

Config::load(dirname(__DIR__));

date_default_timezone_set((string) Config::get('app.timezone', 'Asia/Ho_Chi_Minh'));

$debug = (bool) Config::get('app.debug', false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : E_ALL);

set_exception_handler(static function (Throwable $e) use ($debug): void {
    Logger::error('Uncaught exception: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
    http_response_code(500);
    if ($debug) {
        echo '<h1>Lỗi máy chủ</h1><pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }
    $file = dirname(__DIR__) . '/app/Views/errors/500.php';
    if (is_file($file)) {
        include $file;
    } else {
        echo 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
    }
});

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

Session::start();
Csrf::ensureToken();
Auth::boot();
View::share('authUser', Auth::user());
View::share('cartCount', 0);
View::share('wishlistCount', 0);
View::share('unreadNotifications', 0);

try {
    Database::connection();
    if (class_exists(\App\Services\CartService::class) && Session::id()) {
        View::share('cartCount', \App\Services\CartService::count());
        View::share('wishlistCount', \App\Services\WishlistService::count());
        View::share('unreadNotifications', \App\Services\NotificationService::unreadCount());
        View::share('navCategories', \App\Models\Category::active());
    }
} catch (Throwable $e) {
    // Installer / first boot without DB still allows /install
}

$router = new Router();
require dirname(__DIR__) . '/routes/web.php';
require dirname(__DIR__) . '/routes/admin.php';
require dirname(__DIR__) . '/routes/api.php';

$request = Request::capture();
$router->dispatch($request);
