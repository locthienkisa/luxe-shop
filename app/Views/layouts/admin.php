<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Admin') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="d-flex">
<aside class="admin-sidebar p-3" style="width:240px">
<a class="d-block text-white fw-bold mb-4" href="<?= e(url('/admin')) ?>">Luxe Admin</a>
<?php
$links = [
  '/admin'=>'Dashboard','/admin/san-pham'=>'Sản phẩm','/admin/danh-muc'=>'Danh mục','/admin/don-hang'=>'Đơn hàng',
  '/admin/nguoi-dung'=>'Khách hàng','/admin/voucher'=>'Voucher','/admin/danh-gia'=>'Đánh giá','/admin/banner'=>'Banner',
  '/admin/ton-kho'=>'Tồn kho','/admin/cai-dat'=>'Cài đặt',
];
$path = parse_url($_SERVER['REQUEST_URI']??'/', PHP_URL_PATH);
foreach ($links as $href=>$label):
  $active = $path === $href || ($href !== '/admin' && str_starts_with((string)$path, $href));
?>
<a class="d-block rounded px-3 py-2 mb-1 <?= $active?'active':'' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
<a class="d-block rounded px-3 py-2 mt-4" href="<?= e(url('/')) ?>">← Cửa hàng</a>
</aside>
<main class="flex-grow-1 p-4 bg-light">
<?php include dirname(__DIR__).'/partials/flash.php'; ?>
<?= $content ?>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
