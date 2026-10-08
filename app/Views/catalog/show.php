<?php
$main = $images[0]['path'] ?? 'assets/images/placeholders/product.svg';
$price = product_price($product);
?>
<nav class="mb-3 small"><a href="<?= e(url('/')) ?>">Trang chủ</a> /
    <?php foreach ($cats as $c): ?><a href="<?= e(url('/danh-muc/'.$c['slug'])) ?>"><?= e($c['name']) ?></a> /<?php endforeach; ?>
    <?= e($product['name']) ?>
</nav>
<div class="row g-4">
    <div class="col-md-6">
        <img class="img-fluid rounded w-100" style="aspect-ratio:1;object-fit:cover" src="<?= e(asset($main)) ?>" alt="<?= e($product['name']) ?>">
        <div class="d-flex gap-2 mt-2 flex-wrap">
            <?php foreach ($images as $im): ?>
                <img src="<?= e(asset($im['path'])) ?>" width="72" height="72" class="rounded" alt="" style="object-fit:cover">
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-6">
        <h1 class="h3"><?= e($product['name']) ?></h1>
        <div class="text-muted mb-2">SKU: <?= e($product['sku']) ?> · <?= e($product['brand']) ?></div>
        <div class="h4 price"><?= money($price) ?>
            <?php if ($price < (float)$product['price']): ?><span class="price-old h6"><?= money($product['price']) ?></span><?php endif; ?>
        </div>
        <p><?= e($product['short_description']) ?></p>
        <p>Tồn kho: <strong><?= (int)$product['stock'] ?></strong></p>
        <form method="post" action="<?= e(url('/gio-hang/them')) ?>" class="mb-3">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <?php if ($variants): ?>
                <label class="form-label">Biến thể</label>
                <select name="variant_id" class="form-select mb-2" required>
                    <?php foreach ($variants as $v): ?>
                        <option value="<?= (int)$v['id'] ?>"><?= e(trim($v['color'].' '.$v['size'])) ?> — tồn <?= (int)$v['stock'] ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <div class="input-group mb-3" style="max-width:220px">
                <span class="input-group-text">SL</span>
                <input class="form-control" type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
            </div>
            <button class="btn btn-gold btn-lg" <?= (int)$product['stock']<1?'disabled':'' ?>><i class="fa fa-bag-shopping me-1"></i>Thêm giỏ hàng</button>
        </form>
        <?php if (!empty($authUser)): ?>
            <form method="post" action="<?= e(url('/yeu-thich')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <button class="btn btn-outline-danger"><i class="fa fa-heart me-1"></i>Yêu thích</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<div class="mt-5">
    <h2 class="h5">Mô tả</h2>
    <div><?= nl2br(e($product['description'])) ?></div>
</div>
<div class="mt-5">
    <h2 class="h5">Đánh giá</h2>
    <?php foreach ($reviews as $rv): ?>
        <div class="border-bottom py-3">
            <strong><?= e($rv['full_name']) ?></strong>
            <span class="text-warning"><?php for($i=1;$i<=5;$i++): ?><i class="fa<?= $i<=$rv['rating']?'s':'r' ?> fa-star"></i><?php endfor; ?></span>
            <p class="mb-1"><?= e($rv['content']) ?></p>
            <?php foreach ($rv['images'] as $im): ?><img src="<?= e(asset($im['path'])) ?>" width="80" class="me-1 rounded" alt=""><?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <?php if (!empty($authUser) && $canReview): ?>
        <form class="mt-3" method="post" action="<?= e(url('/danh-gia')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <label class="form-label">Số sao</label>
            <select name="rating" class="form-select mb-2" style="max-width:120px" required>
                <?php for ($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select>
            <textarea name="content" class="form-control mb-2" required minlength="5" placeholder="Cảm nhận của bạn"></textarea>
            <input type="file" name="images[]" accept="image/*" multiple class="form-control mb-2">
            <button class="btn btn-gold">Gửi đánh giá</button>
        </form>
    <?php elseif (!empty($authUser)): ?>
        <p class="text-muted">Bạn cần mua và nhận hàng để đánh giá sản phẩm này.</p>
    <?php endif; ?>
</div>
