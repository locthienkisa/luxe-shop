<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4">
<h1 class="h4">Đặt lại mật khẩu</h1>
<form method="post" action="<?= e(url('/dat-lai-mat-khau')) ?>">
<?= csrf_field() ?>
<input type="hidden" name="token" value="<?= e($token) ?>">
<label class="form-label">Mật khẩu mới</label>
<input class="form-control mb-2" type="password" name="password" required minlength="8">
<label class="form-label">Xác nhận</label>
<input class="form-control mb-3" type="password" name="password_confirmation" required minlength="8">
<button class="btn btn-gold w-100">Cập nhật</button>
</form>
</div></div></div>
