<div class="list-group account-nav">
    <a class="list-group-item list-group-item-action" href="<?= e(url('/tai-khoan')) ?>">Hồ sơ</a>
    <a class="list-group-item list-group-item-action" href="<?= e(url('/tai-khoan/doi-mat-khau')) ?>">Đổi mật khẩu</a>
    <a class="list-group-item list-group-item-action" href="<?= e(url('/tai-khoan/dia-chi')) ?>">Địa chỉ</a>
    <a class="list-group-item list-group-item-action" href="<?= e(url('/don-hang')) ?>">Đơn hàng</a>
    <a class="list-group-item list-group-item-action" href="<?= e(url('/yeu-thich')) ?>">Yêu thích</a>
    <a class="list-group-item list-group-item-action" href="<?= e(url('/thong-bao')) ?>">Thông báo
        <?php if (($unreadNotifications ?? 0) > 0): ?><span class="badge bg-danger"><?= (int)$unreadNotifications ?></span><?php endif; ?>
    </a>
    <form method="post" action="<?= e(url('/dang-xuat')) ?>"><?= csrf_field() ?><button class="list-group-item list-group-item-action text-danger">Đăng xuất</button></form>
</div>
