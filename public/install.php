<?php
/**
 * Local installer. Delete or protect this file after production deploy.
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];
$ok = [];
$done = false;

session_name('luxe_installer');
session_set_cookie_params([
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
$_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
$exts = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'json'];
$missing = array_filter($exts, fn($e) => !extension_loaded($e));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? '127.0.0.1');
    $port = trim($_POST['db_port'] ?? '3306');
    $name = trim($_POST['db_name'] ?? 'luxe_shop');
    $user = trim($_POST['db_user'] ?? 'root');
    $pass = (string)($_POST['db_pass'] ?? '');
    $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $adminEmail = strtolower(trim($_POST['admin_email'] ?? ''));
    $adminPass = (string)($_POST['admin_pass'] ?? '');
    $adminName = trim($_POST['admin_name'] ?? 'Super Admin');

    if (!hash_equals((string)$_SESSION['_csrf'], (string)($_POST['_csrf'] ?? ''))) $errors[] = 'Phiên cài đặt không hợp lệ. Hãy tải lại trang.';
    if (!$phpOk) $errors[] = 'Cần PHP 8.2+';
    if ($missing) $errors[] = 'Thiếu extension: ' . implode(', ', $missing);
    if (!preg_match('/^[A-Za-z0-9.:-]+$/', $host)) $errors[] = 'DB host không hợp lệ.';
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) $errors[] = 'Tên database chỉ được gồm chữ, số và dấu gạch dưới.';
    if (!ctype_digit($port) || (int)$port < 1 || (int)$port > 65535) $errors[] = 'DB port không hợp lệ.';
    if (preg_match('/[\r\n]/', $user . $pass . $appUrl)) $errors[] = 'Thông tin cấu hình chứa ký tự dòng không hợp lệ.';
    $appUrlParts = parse_url($appUrl);
    if (!is_array($appUrlParts) || !in_array(strtolower((string)($appUrlParts['scheme'] ?? '')), ['http', 'https'], true) || empty($appUrlParts['host'])) $errors[] = 'APP_URL phải là địa chỉ HTTP hoặc HTTPS hợp lệ.';
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email admin không hợp lệ';
    if (strlen($adminPass) < 10) $errors[] = 'Mật khẩu admin tối thiểu 10 ký tự';
    if ($adminPass === 'Password123!') $errors[] = 'Không dùng mật khẩu demo mặc định.';

    if (!$errors) {
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $name . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $pdo->exec('USE `' . $name . '`');
            $tableCount = (int)$pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
            if ($tableCount > 0) {
                throw new DomainException('Database đã có bảng. Installer dừng để tránh xóa dữ liệu hiện có.');
            }
            $schema = file_get_contents($root . '/database/schema.sql');
            $schema = preg_replace('/CREATE DATABASE.*?;/s', '', $schema);
            $schema = preg_replace('/USE `luxe_shop`;/', '', $schema);
            $pdo->exec($schema);
            $ok[] = 'Đã tạo bảng.';

            if (!empty($_POST['seed'])) {
                $seed = file_get_contents($root . '/database/seed.sql');
                $seed = preg_replace('/USE `luxe_shop`;/', '', $seed);
                $pdo->exec($seed);
                $ok[] = 'Đã import seed demo.';
            }

            $hash = password_hash($adminPass, PASSWORD_DEFAULT);
            $role = (int)$pdo->query("SELECT id FROM roles WHERE slug='super_admin'")->fetchColumn();
            $st = $pdo->prepare('SELECT id FROM users WHERE email=?');
            $st->execute([$adminEmail]);
            $id = $st->fetchColumn();
            if ($id) {
                $pdo->prepare('UPDATE users SET password_hash=?, role_id=?, must_change_password=1, is_active=1 WHERE id=?')
                    ->execute([$hash, $role, $id]);
            } else {
                $pdo->prepare('INSERT INTO users (role_id,email,password_hash,full_name,is_active,must_change_password) VALUES (?,?,?,?,1,1)')
                    ->execute([$role, $adminEmail, $hash, $adminName]);
            }
            $ok[] = 'Đã tạo admin — bắt buộc đổi mật khẩu khi đăng nhập.';

            $key = bin2hex(random_bytes(16));
            $env = "APP_NAME=\"Luxe Shop\"\nAPP_ENV=local\nAPP_DEBUG=true\nAPP_URL={$appUrl}\nAPP_KEY={$key}\nAPP_TIMEZONE=Asia/Ho_Chi_Minh\n\nDB_HOST={$host}\nDB_PORT={$port}\nDB_DATABASE={$name}\nDB_USERNAME={$user}\nDB_PASSWORD={$pass}\n\nSESSION_NAME=luxe_session\nSESSION_LIFETIME=7200\nSHIPPING_FEE=30000\nFREE_SHIPPING_MIN=500000\nVERIFIED_PURCHASE_ONLY=true\nLOGIN_MAX_ATTEMPTS=5\nLOGIN_LOCKOUT_MINUTES=15\n";
            file_put_contents($root . '/.env', $env);
            $ok[] = 'Đã ghi .env';
            $done = true;
        } catch (Throwable $e) {
            $errors[] = $e instanceof DomainException ? $e->getMessage() : 'Cài đặt thất bại. Kiểm tra kết nối CSDL.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cài đặt Luxe Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light"><div class="container py-5" style="max-width:720px">
<h1 class="h3 mb-3">Installer Luxe Shop</h1>
<ul>
<li>PHP <?= h(PHP_VERSION) ?> <?= $phpOk ? '✓' : '✗' ?></li>
<li>Extensions: <?= $missing ? 'Thiếu '.h(implode(', ',$missing)) : '✓' ?></li>
</ul>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= h($e) ?></div><?php endforeach; ?>
<?php foreach ($ok as $e): ?><div class="alert alert-success"><?= h($e) ?></div><?php endforeach; ?>
<?php if ($done): ?>
<p>Xong. <a href="/">Vào cửa hàng</a> · <a href="/admin">Admin</a>. Hãy xóa <code>public/install.php</code> trên production.</p>
<?php else: ?>
<form method="post" class="card p-4">
<input type="hidden" name="_csrf" value="<?= h($_SESSION['_csrf']) ?>">
<div class="mb-2"><label>DB host</label><input class="form-control" name="db_host" value="127.0.0.1"></div>
<div class="mb-2"><label>DB port</label><input class="form-control" name="db_port" value="3306"></div>
<div class="mb-2"><label>DB name</label><input class="form-control" name="db_name" value="luxe_shop"></div>
<div class="mb-2"><label>DB user</label><input class="form-control" name="db_user" value="root"></div>
<div class="mb-2"><label>DB password</label><input class="form-control" type="password" name="db_pass"></div>
<div class="mb-2"><label>APP_URL</label><input class="form-control" name="app_url" value="http://localhost:8080" required></div>
<div class="mb-2"><label>Admin email</label><input class="form-control" type="email" name="admin_email" required></div>
<div class="mb-2"><label>Admin password (tối thiểu 10 ký tự, không dùng Password123!)</label><input class="form-control" type="password" name="admin_pass" required minlength="10"></div>
<div class="mb-2"><label>Tên admin</label><input class="form-control" name="admin_name" value="Super Admin"></div>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="seed" value="1" id="s" checked><label class="form-check-label" for="s">Import dữ liệu demo</label></div>
<button class="btn btn-dark">Cài đặt</button>
</form>
<?php endif; ?>
</div></body></html>
