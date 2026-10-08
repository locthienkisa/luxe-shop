<?php if ($heroes): ?>
<div id="hero" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($heroes as $i => $b): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="hero-slide d-flex align-items-center p-4 p-lg-5" style="<?= $b['image'] ? 'background:url(' . e(asset($b['image'])) . ') center/cover;' : '' ?>">
                    <div class="p-3 p-md-5 rounded" style="background:rgba(15,39,68,.55);max-width:640px">
                        <h1><?= e($b['title']) ?></h1>
                        <p class="lead"><?= e($b['subtitle']) ?></p>
                        <?php if ($b['link']): ?><a class="btn btn-gold btn-lg" href="<?= e($b['link']) ?>">Mua ngay</a><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($heroes) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#hero" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#hero" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    <?php endif; ?>
</div>
<?php endif; ?>

<h2 class="section-title h4 mb-3">Danh mục</h2>
<div class="row g-3 mb-5">
    <?php foreach ($categories as $c): ?>
        <div class="col-6 col-md-4 col-lg">
            <a class="card text-center p-3 text-decoration-none text-dark h-100" href="<?= e(url('/danh-muc/' . $c['slug'])) ?>">
                <i class="fa-solid fa-layer-group fa-2x mb-2 text-secondary"></i>
                <strong><?= e($c['name']) ?></strong>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="section-title h4 mb-3">Sản phẩm nổi bật</h2>
<div class="row"><?php foreach ($featured as $p) { include dirname(__DIR__) . '/partials/product-card.php'; } ?></div>

<h2 class="section-title h4 mb-3">Flash sale</h2>
<div class="row"><?php foreach ($sale as $p) { include dirname(__DIR__) . '/partials/product-card.php'; } ?></div>

<h2 class="section-title h4 mb-3">Bán chạy</h2>
<div class="row"><?php foreach ($best as $p) { include dirname(__DIR__) . '/partials/product-card.php'; } ?></div>

<h2 class="section-title h4 mb-3">Hàng mới</h2>
<div class="row"><?php foreach ($new as $p) { include dirname(__DIR__) . '/partials/product-card.php'; } ?></div>

<?php if ($promos): ?>
    <?php foreach ($promos as $b): ?>
        <a href="<?= e($b['link'] ?: url('/san-pham')) ?>" class="d-block mb-4">
            <div class="hero-slide d-flex align-items-center justify-content-center p-5">
                <div class="text-center"><h3><?= e($b['title']) ?></h3><p><?= e($b['subtitle']) ?></p></div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<h2 class="section-title h4 mb-3">Khách hàng nói gì</h2>
<div class="row g-3">
    <?php foreach ($reviews as $rv): ?>
        <div class="col-md-4">
            <div class="card h-100 p-3">
                <div class="text-warning mb-2"><?php for ($i=1;$i<=5;$i++): ?><i class="fa<?= $i<=$rv['rating']?'s':'r' ?> fa-star"></i><?php endfor; ?></div>
                <p><?= e($rv['content']) ?></p>
                <div class="small text-muted"><?= e($rv['full_name']) ?> · <?= e($rv['product_name']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
