<div class="row g-4"><div class="col-md-3"><?php include dirname(__DIR__).'/partials/account-nav.php'; ?></div>
<div class="col-md-9">
<h1 class="h4">Thông báo</h1>
<form method="post" action="<?= e(url('/thong-bao/doc-tat')) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary mb-3">Đánh dấu đã đọc</button></form>
<?php foreach ($items as $n): ?>
<div class="card p-3 mb-2 <?= $n['is_read']?'':'border-warning' ?>">
<strong><?= e($n['title']) ?></strong>
<p class="mb-1"><?= e($n['body']) ?></p>
<small class="text-muted"><?= e($n['created_at']) ?></small>
<?php if (!$n['is_read']): ?>
<form method="post" action="<?= e(url('/thong-bao/doc')) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$n['id'] ?>"><button class="btn btn-sm btn-link">Đã đọc</button></form>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div></div>
