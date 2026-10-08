<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h1 class="h4 mb-3">Đăng ký</h1>
            <form method="post" action="<?= e(url('/dang-ky')) ?>">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">Họ tên</label><input class="form-control" name="full_name" required value="<?= e(old('full_name')) ?>"></div>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="<?= e(old('email')) ?>"></div>
                <div class="mb-3"><label class="form-label">Điện thoại</label><input class="form-control" name="phone" value="<?= e(old('phone')) ?>"></div>
                <div class="mb-3"><label class="form-label">Mật khẩu</label><input class="form-control" type="password" name="password" required minlength="8"></div>
                <div class="mb-3"><label class="form-label">Xác nhận mật khẩu</label><input class="form-control" type="password" name="password_confirmation" required minlength="8"></div>
                <button class="btn btn-gold w-100">Đăng ký</button>
            </form>
        </div>
    </div>
</div>
