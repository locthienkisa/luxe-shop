<h1 class="h4">Đơn <?= e($order['order_number']) ?></h1>
<p><?= e($order['shipping_name']) ?> · <?= e($order['shipping_phone']) ?><br><?= e($order['shipping_address']) ?></p>
<p>Thanh toán: <?= e($order['payment_status']) ?> / <?= e($order['payment_method']) ?> · Tổng <?= money($order['total']) ?></p>
<table class="table"><thead><tr><th>SP</th><th>SL</th><th>Giá</th></tr></thead><tbody>
<?php foreach ($items as $it): ?><tr><td><?= e($it['product_name']) ?></td><td><?= (int)$it['quantity'] ?></td><td><?= money($it['line_total']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<div class="timeline mb-4">
<?php foreach ($history as $h): ?><div class="timeline-item"><?= e(order_status_label($h['to_status'])) ?> — <?= e($h['created_at']) ?> <small><?= e($h['note']??'') ?></small></div><?php endforeach; ?>
</div>
<form method="post" action="<?= e(url('/admin/don-hang/trang-thai')) ?>" class="d-flex gap-2 mb-2">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
<select name="status" class="form-select" style="max-width:220px">
<?php foreach (['confirmed','processing','shipping','delivered','cancelled','refunded'] as $s): ?>
<option value="<?= $s ?>"><?= e(order_status_label($s)) ?></option>
<?php endforeach; ?>
</select>
<input class="form-control" name="note" placeholder="Ghi chú">
<button class="btn btn-gold">Đổi trạng thái</button>
</form>
<form method="post" action="<?= e(url('/admin/don-hang/thanh-toan')) ?>">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
<button class="btn btn-outline-success">Xác nhận đã thanh toán</button>
</form>
