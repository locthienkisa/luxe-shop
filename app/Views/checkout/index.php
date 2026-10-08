<h1 class="h4 mb-3">Thanh toán</h1>
<div class="row g-4">
<div class="col-lg-7">
<form method="post" action="<?= e(url('/thanh-toan')) ?>">
<?= csrf_field() ?>
<h2 class="h6">Thông tin khách hàng</h2>
<p><?= e($user['full_name']) ?> · <?= e($user['email']) ?></p>
<h2 class="h6">Địa chỉ giao hàng</h2>
<?php if ($addresses): ?>
    <?php foreach ($addresses as $a): ?>
        <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="address_id" value="<?= (int)$a['id'] ?>" <?= $a['is_default']?'checked':'' ?> required>
            <label class="form-check-label"><?= e($a['full_name']) ?> — <?= e(\App\Models\Address::format($a)) ?> (<?= e($a['phone']) ?>)</label>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="mb-2"><input class="form-control" name="shipping_name" placeholder="Họ tên" required value="<?= e($user['full_name']) ?>"></div>
    <div class="mb-2"><input class="form-control" name="shipping_phone" placeholder="Điện thoại" required></div>
    <div class="mb-2"><input class="form-control" name="shipping_address" placeholder="Địa chỉ đầy đủ" required></div>
<?php endif; ?>
<h2 class="h6 mt-3">Thanh toán</h2>
<?php foreach ($gateways as $g): ?>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" value="<?= e($g->code()) ?>" checked>
        <label class="form-check-label"><?= e($g->label()) ?></label>
    </div>
<?php endforeach; ?>
<textarea class="form-control my-3" name="notes" placeholder="Ghi chú đơn hàng"></textarea>
<button class="btn btn-gold btn-lg">Xác nhận đặt hàng</button>
</form>
</div>
<div class="col-lg-5">
<div class="card p-3">
<h2 class="h6">Đơn hàng</h2>
<?php foreach ($items as $it): ?>
    <div class="d-flex justify-content-between small mb-1">
        <span><?= e($it['name']) ?> × <?= (int)$it['quantity'] ?></span>
        <span><?= money($it['line_total']) ?></span>
    </div>
<?php endforeach; ?>
<hr>
<div class="d-flex justify-content-between"><span>Tạm tính</span><?= money($subtotal) ?></div>
<div class="d-flex justify-content-between"><span>Giảm</span>-<?= money($discount) ?></div>
<div class="d-flex justify-content-between"><span>Ship</span><?= money($shipping) ?></div>
<div class="d-flex justify-content-between fw-bold mt-2"><span>Tổng</span><?= money($total) ?></div>
<?php if ($coupon): ?><div class="small text-success mt-2">Mã: <?= e($coupon['code']) ?></div><?php endif; ?>
</div>
</div>
</div>
