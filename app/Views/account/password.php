<div class="row g-4"><div class="col-md-3"><?php include dirname(__DIR__).'/partials/account-nav.php'; ?></div>
<div class="col-md-9"><div class="card p-4">
<h1 class="h4">Đổi mật khẩu</h1>
<form method="post">
<?= csrf_field() ?>
<?php if ((int)($authUser['must_change_password']??0)!==1): ?>
<div class="mb-3"><label class="form-label">Mật khẩu hiện tại</label><input class="form-control" type="password" name="current_password" required></div>
<?php endif; ?>
<div class="mb-3"><label class="form-label">Mật khẩu mới</label><input class="form-control" type="password" name="password" required minlength="8"></div>
<div class="mb-3"><label class="form-label">Xác nhận</label><input class="form-control" type="password" name="password_confirmation" required minlength="8"></div>
<button class="btn btn-gold">Cập nhật</button>
</form>
</div></div></div>
