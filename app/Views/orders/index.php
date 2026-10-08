<div class="row g-4"><div class="col-md-3"><?php include dirname(__DIR__).'/partials/account-nav.php'; ?></div>
<div class="col-md-9">
<h1 class="h4">Đơn hàng</h1>
<div class="table-responsive">
<table class="table bg-white">
<thead><tr><th>Mã</th><th>Ngày</th><th>Tổng</th><th>Trạng thái</th><th></th></tr></thead>
<tbody>
<?php foreach ($orders as $o): ?>
<tr>
<td><?= e($o['order_number']) ?></td>
<td><?= e($o['created_at']) ?></td>
<td><?= money($o['total']) ?></td>
<td><?= e(order_status_label($o['status'])) ?></td>
<td><a href="<?= e(url('/don-hang/'.$o['id'])) ?>">Chi tiết</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div></div>
