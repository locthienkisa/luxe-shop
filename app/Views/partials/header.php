<nav class="navbar navbar-expand-lg navbar-lx sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(url('/')) ?>"><i class="fa-solid fa-gem me-2 text-warning"></i><?= e(setting('site_name', 'Luxe Shop')) ?></a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse gap-3" id="navMain">
            <ul class="navbar-nav me-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Danh mục</a>
                    <ul class="dropdown-menu">
                        <?php foreach (($navCategories ?? []) as $c): ?>
                            <li><a class="dropdown-item" href="<?= e(url('/danh-muc/' . $c['slug'])) ?>"><?= e($c['name']) ?></a></li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= e(url('/san-pham')) ?>">Tất cả sản phẩm</a></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex flex-grow-1 search-wrap mx-lg-4 my-2 my-lg-0" action="<?= e(url('/san-pham')) ?>" method="get">
                <input class="form-control" type="search" name="q" placeholder="Tìm tên, SKU, thương hiệu..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="Tìm kiếm">
                <button class="btn btn-gold ms-2" type="submit"><i class="fa fa-search"></i></button>
            </form>
            <div class="d-flex align-items-center gap-2 ms-lg-auto">
                <?php if (!empty($authUser)): ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= e(url('/tai-khoan')) ?>"><i class="fa fa-user me-1"></i><?= e($authUser['full_name']) ?></a>
                    <?php if (\App\Core\Auth::isStaff()): ?>
                        <a class="btn btn-warning btn-sm" href="<?= e(url('/admin')) ?>">Admin</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= e(url('/dang-nhap')) ?>">Đăng nhập</a>
                <?php endif; ?>
                <a class="btn btn-outline-light icon-btn position-relative" href="<?= e(url('/yeu-thich')) ?>" title="Yêu thích">
                    <i class="fa fa-heart"></i>
                    <?php if (($wishlistCount ?? 0) > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= (int) $wishlistCount ?></span><?php endif; ?>
                </a>
                <a class="btn btn-gold icon-btn position-relative" href="<?= e(url('/gio-hang')) ?>" title="Giỏ hàng">
                    <i class="fa fa-bag-shopping"></i>
                    <?php if (($cartCount ?? 0) > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= (int) $cartCount ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>
