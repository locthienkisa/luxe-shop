<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Luxe Shop') ?> — <?= e(setting('site_name', 'Luxe Shop')) ?></title>
    <meta name="description" content="<?= e($meta_description ?? setting('meta_description', 'Cửa hàng trực tuyến')) ?>">
    <link rel="canonical" href="<?= e($canonical ?? url($_SERVER['REQUEST_URI'] ?? '/')) ?>">
    <meta property="og:title" content="<?= e($title ?? 'Luxe Shop') ?>">
    <meta property="og:description" content="<?= e($meta_description ?? '') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical ?? url('/')) ?>">
    <?php if (!empty($og_image)): ?>
        <meta property="og:image" content="<?= e(asset($og_image)) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body>
<?php include dirname(__DIR__) . '/partials/header.php'; ?>
<main class="py-4">
    <div class="container">
        <?php include dirname(__DIR__) . '/partials/flash.php'; ?>
        <?= $content ?>
    </div>
</main>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('assets/js/app.js')) ?>"></script>
</body>
</html>
