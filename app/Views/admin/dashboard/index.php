<h1 class="h4 mb-4">Dashboard</h1>
<div class="row g-3 mb-4">
<?php
$cards = [
  ['Tổng doanh thu', money($stats['revenue'])],
  ['Hôm nay', money($stats['today'])],
  ['Tháng này', money($stats['month'])],
  ['Tổng đơn', $stats['orders']],
  ['Đang xử lý', $stats['processing']],
  ['Hoàn thành', $stats['done']],
  ['Khách hàng', $stats['customers']],
  ['Sản phẩm', $stats['products']],
];
foreach ($cards as $c): ?>
<div class="col-6 col-lg-3"><div class="card stat-card p-3"><div class="small text-muted"><?= e($c[0]) ?></div><div class="h5 mb-0"><?= e((string)$c[1]) ?></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-3">
<div class="col-lg-8"><div class="card p-3"><canvas id="revDay"></canvas></div></div>
<div class="col-lg-4"><div class="card p-3"><canvas id="stPie"></canvas></div></div>
<div class="col-lg-6"><div class="card p-3"><canvas id="revMonth"></canvas></div></div>
<div class="col-lg-6">
<div class="card p-3">
<h6>Top bán chạy</h6>
<ul class="mb-0"><?php foreach ($top as $t): ?><li><?= e($t['product_name']) ?> — <?= (int)$t['qty'] ?></li><?php endforeach; ?></ul>
</div>
<div class="card p-3 mt-3">
<h6>Sắp hết hàng</h6>
<ul><?php foreach ($low as $p): ?><li><?= e($p['name']) ?> (<?= (int)$p['stock'] ?>)</li><?php endforeach; ?></ul>
</div>
</div>
</div>
<script>
new Chart(document.getElementById('revDay'), {type:'line', data:{labels:<?= json_encode(array_column($days,'d')) ?>, datasets:[{label:'Doanh thu ngày', data:<?= json_encode(array_map('floatval', array_column($days,'t'))) ?>, borderColor:'#c9a227'}]}});
new Chart(document.getElementById('revMonth'), {type:'bar', data:{labels:<?= json_encode(array_column($months,'m')) ?>, datasets:[{label:'Doanh thu tháng', data:<?= json_encode(array_map('floatval', array_column($months,'t'))) ?>, backgroundColor:'#0f2744'}]}});
new Chart(document.getElementById('stPie'), {type:'doughnut', data:{labels:<?= json_encode(array_column($byStatus,'status')) ?>, datasets:[{data:<?= json_encode(array_map('intval', array_column($byStatus,'c'))) ?>}]}});
</script>
