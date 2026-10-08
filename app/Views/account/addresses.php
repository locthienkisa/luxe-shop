<div class="row g-4"><div class="col-md-3"><?php include dirname(__DIR__).'/partials/account-nav.php'; ?></div>
<div class="col-md-9">
<h1 class="h4">Địa chỉ</h1>
<?php foreach ($addresses as $a): ?>
<div class="card p-3 mb-2 d-flex flex-row justify-content-between">
<div><strong><?= e($a['label'] ?: 'Địa chỉ') ?></strong><?php if ($a['is_default']): ?> <span class="badge bg-secondary">Mặc định</span><?php endif; ?>
<div><?= e($a['full_name']) ?> · <?= e($a['phone']) ?></div>
<div class="text-muted"><?= e(\App\Models\Address::format($a)) ?></div></div>
<form method="post" action="<?= e(url('/tai-khoan/dia-chi/xoa')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><button class="btn btn-sm btn-outline-danger">Xóa</button></form>
</div>
<?php endforeach; ?>
<div class="card p-3 mt-3">
<h2 class="h6">Thêm địa chỉ</h2>
<form method="post">
<?= csrf_field() ?>
<div class="row g-2">
<div class="col-md-4"><input class="form-control" name="label" placeholder="Nhãn (Nhà, Công ty)"></div>
<div class="col-md-4"><input class="form-control" name="full_name" placeholder="Họ tên" required></div>
<div class="col-md-4"><input class="form-control" name="phone" placeholder="Điện thoại" required></div>
<div class="col-12"><input class="form-control" name="line1" placeholder="Địa chỉ" required></div>
<div class="col-md-4"><input class="form-control" name="ward" placeholder="Phường/Xã"></div>
<div class="col-md-4"><input class="form-control" name="district" placeholder="Quận/Huyện" required></div>
<div class="col-md-4"><input class="form-control" name="city" placeholder="Tỉnh/TP" required></div>
<div class="col-12 form-check"><input class="form-check-input" type="checkbox" name="is_default" value="1" id="def"><label class="form-check-label" for="def">Mặc định</label></div>
</div>
<button class="btn btn-gold mt-2">Lưu địa chỉ</button>
</form>
</div>
</div></div>
