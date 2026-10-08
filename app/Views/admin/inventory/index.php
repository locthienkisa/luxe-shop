<h1 class="h4">Tồn kho</h1>
<h2 class="h6">Sắp hết</h2>
<ul><?php foreach ($low as $p): ?><li><?= e($p['name']) ?> — <?= (int)$p['stock'] ?></li><?php endforeach; ?></ul>
<form method="post" class="card p-3 mb-4 row g-2">
<?= csrf_field() ?>
<div class="col-md-3"><input class="form-control" type="number" name="product_id" placeholder="Product ID" required></div>
<div class="col-md-3"><input class="form-control" type="number" name="change_qty" placeholder="+/- số lượng" required></div>
<div class="col-md-4"><input class="form-control" name="note" placeholder="Ghi chú"></div>
<div class="col-md-2"><button class="btn btn-gold">Điều chỉnh</button></div>
</form>
<table class="table"><thead><tr><th>Thời gian</th><th>SP</th><th>Δ</th><th>Lý do</th></tr></thead><tbody>
<?php foreach ($logs as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['name']) ?></td><td><?= (int)$l['change_qty'] ?></td><td><?= e($l['reason']) ?></td></tr><?php endforeach; ?>
</tbody></table>
