<h1 class="h4">Đơn hàng</h1>
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="<?= e($filters['q']??'') ?>" placeholder="Mã / tên / SĐT"></div>
<div class="col-md-3">
<select name="status" class="form-select">
<option value="">Tất cả trạng thái</option>
<?php foreach (['pending','confirmed','processing','shipping','delivered','cancelled','refunded'] as $s): ?>
<option value="<?= $s ?>" <?= ($filters['status']??'')===$s?'selected':'' ?>><?= e(order_status_label($s)) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-2"><button class="btn btn-outline-secondary">Lọc</button></div>
</form>
<table class="table bg-white">
<thead><tr><th>Mã</th><th>Khách</th><th>Tổng</th><th>TT</th><th>Ngày</th></tr></thead>
<tbody>
<?php foreach ($orders as $o): ?>
<tr>
<td><a href="<?= e(url('/admin/don-hang/'.$o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
<td><?= e($o['email']) ?></td>
<td><?= money($o['total']) ?></td>
<td><?= e(order_status_label($o['status'])) ?></td>
<td><?= e($o['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
