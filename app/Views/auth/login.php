<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-4">
            <h1 class="h4 mb-3">Đăng nhập</h1>
            <form method="post" action="<?= e(url('/dang-nhap')) ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required value="<?= e(old('email')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input class="form-control" type="password" name="password" required minlength="8">
                </div>
                <button class="btn btn-gold w-100">Đăng nhập</button>
            </form>
            <div class="mt-3 small">
                <a href="<?= e(url('/quen-mat-khau')) ?>">Quên mật khẩu?</a> ·
                <a href="<?= e(url('/dang-ky')) ?>">Tạo tài khoản</a>
            </div>
        </div>
    </div>
</div>
