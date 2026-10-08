<h1 class="h4">Đơn <?= e($order['order_number']) ?></h1>
<p>Trạng thái: <strong><?= e(order_status_label($order['status'])) ?></strong> · Thanh toán: <?= e($order['payment_status']) ?> (<?= e($order['payment_method']) ?>)</p>
<p>Giao tới: <?= e($order['shipping_name']) ?> — <?= e($order['shipping_address']) ?> (<?= e($order['shipping_phone']) ?>)</p>
<table class="table bg-white">
<thead><tr><th>SP</th><th>SL</th><th>Giá</th><th>Tổng</th></tr></thead>
<tbody>
<?php foreach ($items as $it): ?>
<tr><td><?= e($it['product_name']) ?> <small><?= e($it['sku']) ?></small></td><td><?= (int)$it['quantity'] ?></td><td><?= money($it['unit_price']) ?></td><td><?= money($it['line_total']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<p>Tạm tính <?= money($order['subtotal']) ?> · Giảm <?= money($order['discount']) ?> · Ship <?= money($order['shipping_fee']) ?> · <strong>Tổng <?= money($order['total']) ?></strong></p>
<h2 class="h6">Timeline</h2>
<div class="timeline">
<?php foreach ($history as $h): ?>
<div class="timeline-item"><strong><?= e(order_status_label($h['to_status'])) ?></strong> — <?= e($h['created_at']) ?><div class="small text-muted"><?= e($h['note']??'') ?></div></div>
<?php endforeach; ?>
</div>
<?php if (in_array($order['status'], ['pending','confirmed'], true)): ?>
<form method="post" action="<?= e(url('/don-hang/huy')) ?>" class="mt-3" onsubmit="return confirm('Hủy đơn?')">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
<button class="btn btn-outline-danger">Hủy đơn</button>
</form>
<?php endif; ?>
