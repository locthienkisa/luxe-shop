<h1 class="h4">Voucher</h1>
<table class="table bg-white"><thead><tr><th>Mã</th><th>Loại</th><th>Giá trị</th><th>Hạn</th><th>Dùng</th><th></th></tr></thead><tbody>
<?php foreach ($coupons as $c): ?>
<tr>
<td><?= e($c['code']) ?></td><td><?= e($c['discount_type']) ?></td><td><?= e($c['discount_value']) ?></td>
<td><?= e($c['ends_at']) ?></td><td><?= (int)$c['used_count'] ?>/<?= e($c['usage_limit']??'∞') ?></td>
<td><form method="post" action="<?= e(url('/admin/voucher/xoa')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn-link text-danger p-0">Xóa</button></form></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<div class="card p-3">
<h2 class="h6">Tạo voucher</h2>
<form method="post" class="row g-2">
<?= csrf_field() ?>
<div class="col-md-2"><input class="form-control" name="code" placeholder="CODE" required></div>
<div class="col-md-2"><select name="discount_type" class="form-select"><option value="percent">%</option><option value="fixed">Cố định</option></select></div>
<div class="col-md-2"><input class="form-control" type="number" step="0.01" name="discount_value" required></div>
<div class="col-md-2"><input class="form-control" type="number" name="min_order" placeholder="Min order"></div>
<div class="col-md-2"><input class="form-control" type="number" name="max_discount" placeholder="Max giảm"></div>
<div class="col-md-3"><input class="form-control" type="datetime-local" name="starts_at" required></div>
<div class="col-md-3"><input class="form-control" type="datetime-local" name="ends_at" required></div>
<div class="col-md-2"><input class="form-control" type="number" name="usage_limit" placeholder="Limit"></div>
<div class="col-md-2"><input class="form-control" type="number" name="per_user_limit" value="1"></div>
<div class="col-md-2 form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Active</div>
<div class="col-12"><button class="btn btn-gold">Tạo</button></div>
</form>
</div>
