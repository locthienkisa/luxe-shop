<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4">
<h1 class="h4">Quên mật khẩu</h1>
<form method="post" action="<?= e(url('/quen-mat-khau')) ?>">
<?= csrf_field() ?>
<label class="form-label">Email</label>
<input class="form-control mb-3" type="email" name="email" required>
<button class="btn btn-gold w-100">Gửi liên kết</button>
</form>
</div></div></div>
