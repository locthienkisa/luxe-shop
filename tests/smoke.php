<?php
/**
 * Smoke tests — chạy: php tests/smoke.php (cần DB + .env)
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/Helpers/functions.php';
require $root . '/app/Core/Config.php';
require $root . '/app/Core/Logger.php';
require $root . '/app/Core/Database.php';

spl_autoload_register(static function (string $class) use ($root): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $path = $root . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($path)) require $path;
});

\App\Core\Config::load($root);
$pdo = \App\Core\Database::connection();

$fail = 0;
function check(string $name, bool $ok): void {
    global $fail;
    echo ($ok ? 'OK  ' : 'FAIL') . " $name\n";
    if (!$ok) $fail++;
}

check('users seeded', (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() >= 5);
check('products 20', (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() >= 20);
check('categories 5', (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() >= 5);
check('orders 5', (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn() >= 5);
check('password hashed', (bool)$pdo->query("SELECT password_hash LIKE '\\$2y\\$%' FROM users WHERE email='admin@luxe-shop.local'")->fetchColumn());
check('admin role', (string)$pdo->query("SELECT r.slug FROM users u JOIN roles r ON r.id=u.role_id WHERE email='admin@luxe-shop.local'")->fetchColumn() === 'super_admin');

$p = \App\Models\Product::search(['q' => 'iPhone'], 0, 10);
check('search iphone', $p['total'] >= 1);

$c = \App\Models\Coupon::findByCode('WELCOME10');
check('coupon exists', $c !== null);

echo $fail ? "Failed: $fail\n" : "All smoke tests passed.\n";
exit($fail ? 1 : 0);
