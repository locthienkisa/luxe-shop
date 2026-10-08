<?php
$img = $p['image'] ?? null;
$src = $img ? asset($img) : asset('assets/images/placeholders/product.svg');
$price = product_price($p);
$onSale = (float) $price < (float) $p['price'];
?>
<div class="col-6 col-md-4 col-lg-3">
    <div class="card product-card mb-4">
        <a href="<?= e(url('/san-pham/' . $p['slug'])) ?>">
            <img src="<?= e($src) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        </a>
        <div class="card-body">
            <div class="small text-muted"><?= e($p['brand'] ?? '') ?></div>
            <a class="text-decoration-none text-dark fw-semibold" href="<?= e(url('/san-pham/' . $p['slug'])) ?>"><?= e($p['name']) ?></a>
            <div class="mt-2">
                <span class="price"><?= money($price) ?></span>
                <?php if ($onSale): ?><span class="price-old ms-1"><?= money($p['price']) ?></span>
                    <span class="badge badge-sale ms-1">Sale</span><?php endif; ?>
            </div>
            <div class="small text-warning mt-1">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa<?= $i <= round((float)$p['rating_avg']) ? 's' : 'r' ?> fa-star"></i>
                <?php endfor; ?>
                <span class="text-muted">(<?= (int) $p['rating_count'] ?>)</span>
            </div>
        </div>
    </div>
</div>
