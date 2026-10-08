<h1 class="h4 mb-3">Giỏ hàng</h1>
<?php if (!$items): ?>
    <p>Giỏ hàng trống. <a href="<?= e(url('/san-pham')) ?>">Tiếp tục mua sắm</a></p>
<?php else: ?>
<div class="table-responsive">
<table class="table align-middle bg-white">
<thead><tr><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Tạm tính</th><th></th></tr></thead>
<tbody>
<?php foreach ($items as $it): ?>
<tr>
<td>
    <div class="d-flex gap-2 align-items-center">
        <img src="<?= e(asset($it['image'] ?: 'assets/images/placeholders/product.svg')) ?>" width="64" height="64" class="rounded" style="object-fit:cover" alt="">
        <div>
            <a href="<?= e(url('/san-pham/'.$it['slug'])) ?>"><?= e($it['name']) ?></a>
            <div class="small text-muted"><?= e(trim(($it['color']??'').' '.($it['size']??''))) ?></div>
        </div>
    </div>
</td>
<td><?= money($it['unit_price']) ?></td>
<td>
<form method="post" action="<?= e(url('/gio-hang/cap-nhat')) ?>" class="d-flex" style="max-width:140px">
<?= csrf_field() ?>
<input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
<input class="form-control" type="number" name="quantity" value="<?= (int)$it['quantity'] ?>" min="1" max="<?= (int)$it['stock'] ?>">
<button class="btn btn-sm btn-outline-secondary">OK</button>
</form>
</td>
<td><?= money($it['line_total']) ?></td>
<td>
<form method="post" action="<?= e(url('/gio-hang/xoa')) ?>">
<?= csrf_field() ?>
<input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
<button class="btn btn-sm btn-outline-danger">&times;</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="row">
<div class="col-md-6">
<form method="post" action="<?= e(url('/gio-hang/coupon')) ?>" class="d-flex gap-2">
<?= csrf_field() ?>
<input class="form-control" name="code" placeholder="Mã giảm giá" value="<?= e($coupon['code'] ?? '') ?>">
<button class="btn btn-outline-dark">Áp dụng</button>
</form>
<form method="post" action="<?= e(url('/gio-hang/xoa-tat')) ?>" class="mt-2"><?= csrf_field() ?><button class="btn btn-link text-danger px-0">Xóa giỏ hàng</button></form>
</div>
<div class="col-md-6">
<div class="card p-3">
<div class="d-flex justify-content-between"><span>Tạm tính</span><strong><?= money($subtotal) ?></strong></div>
<div class="d-flex justify-content-between"><span>Giảm giá</span><strong>-<?= money($discount) ?></strong></div>
<div class="d-flex justify-content-between"><span>Vận chuyển</span><strong><?= money($shipping) ?></strong></div>
<hr>
<div class="d-flex justify-content-between h5"><span>Tổng</span><span class="price"><?= money($total) ?></span></div>
<a class="btn btn-gold w-100 mt-2" href="<?= e(url('/thanh-toan')) ?>">Thanh toán</a>
</div>
</div>
</div>
<?php endif; ?>
