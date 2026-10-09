<div class="row">
    <aside class="col-lg-3 mb-4">
        <form method="get" class="card p-3">
            <h6>Bộ lọc</h6>
            <?php if (empty($category)): ?>
            <label class="form-label">Danh mục</label>
            <select name="category" class="form-select mb-2">
                <option value="">Tất cả</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c['slug']) ?>" <?= ($filters['category']??'')===$c['slug']?'selected':'' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <label class="form-label">Thương hiệu</label>
            <select name="brand" class="form-select mb-2">
                <option value="">Tất cả</option>
                <?php foreach ($brands as $b): ?>
                    <option <?= ($filters['brand']??'')===$b['brand']?'selected':'' ?>><?= e($b['brand']) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="form-label">Giá từ</label>
            <input class="form-control mb-2" type="number" name="min_price" value="<?= e($filters['min_price']??'') ?>">
            <label class="form-label">Đến</label>
            <input class="form-control mb-2" type="number" name="max_price" value="<?= e($filters['max_price']??'') ?>">
            <label class="form-label">Đánh giá tối thiểu</label>
            <select name="rating" class="form-select mb-2">
                <option value="">Tất cả</option>
                <?php for ($i=5;$i>=1;$i--): ?>
                    <option value="<?= $i ?>" <?= (string)($filters['rating']??'')===(string)$i?'selected':'' ?>><?= $i ?>+ sao</option>
                <?php endfor; ?>
            </select>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock'])?'checked':'' ?> id="stk">
                <label class="form-check-label" for="stk">Còn hàng</label>
            </div>
            <input type="hidden" name="q" value="<?= e($filters['q']??'') ?>">
            <input type="hidden" name="sort" value="<?= e($filters['sort']??'newest') ?>">
            <button class="btn btn-gold w-100">Lọc</button>
        </form>
    </aside>
    <div class="col-lg-9">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1 class="h4 mb-0"><?= e($title) ?> <small class="text-muted">(<?= (int)$meta['total'] ?>)</small></h1>
            <form method="get" class="d-flex">
                <?php foreach ($filters as $k=>$v): if ($k==='sort') continue; if ($v===''||$v===null) continue; ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e((string)$v) ?>">
                <?php endforeach; ?>
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="newest" <?= ($filters['sort']??'')==='newest'?'selected':'' ?>>Mới nhất</option>
                    <option value="price_asc" <?= ($filters['sort']??'')==='price_asc'?'selected':'' ?>>Giá tăng</option>
                    <option value="price_desc" <?= ($filters['sort']??'')==='price_desc'?'selected':'' ?>>Giá giảm</option>
                    <option value="best" <?= ($filters['sort']??'')==='best'?'selected':'' ?>>Bán chạy</option>
                    <option value="rating" <?= ($filters['sort']??'')==='rating'?'selected':'' ?>>Đánh giá</option>
                </select>
            </form>
        </div>
        <?php if ($products): ?>
            <div class="row">
                <?php foreach ($products as $p) { include dirname(__DIR__) . '/partials/product-card.php'; } ?>
            </div>
        <?php else: ?>
            <div class="empty-state text-center bg-white p-5">
                <h2 class="h5">Không tìm thấy sản phẩm</h2>
                <p class="text-muted mb-3">Thử thay đổi từ khóa hoặc bộ lọc để xem thêm sản phẩm.</p>
                <a class="btn btn-outline-dark" href="<?= e(url('/san-pham')) ?>">Xem tất cả sản phẩm</a>
            </div>
        <?php endif; ?>
        <?php if ($meta['pages'] > 1): ?>
            <nav><ul class="pagination">
                <?php for ($i=1;$i<=$meta['pages'];$i++): ?>
                    <li class="page-item <?= $i===$meta['page']?'active':'' ?>">
                        <a class="page-link" href="?<?= e(http_build_query(array_merge($_GET, ['page'=>$i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul></nav>
        <?php endif; ?>
    </div>
</div>
