<div class="row g-4">
    <div class="col-md-3">
        <?php include dirname(__DIR__) . '/partials/account-nav.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card p-4">
            <h1 class="h4">Hồ sơ</h1>
            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">Họ tên</label><input class="form-control" name="full_name" required value="<?= e($user['full_name']) ?>"></div>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">Điện thoại</label><input class="form-control" name="phone" value="<?= e($user['phone'] ?? '') ?>"></div>
                <div class="mb-3"><label class="form-label">Avatar</label><input class="form-control" type="file" name="avatar" accept="image/*"></div>
                <button class="btn btn-gold">Lưu</button>
            </form>
        </div>
    </div>
</div>
