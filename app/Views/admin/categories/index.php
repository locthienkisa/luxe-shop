<h1 class="h4">Danh mục</h1>
<table class="table bg-white"><thead><tr><th>ID</th><th>Tên</th><th>Slug</th><th></th></tr></thead><tbody>
<?php foreach ($categories as $c): ?>
<tr><td><?= (int)$c['id'] ?></td><td><?= e($c['name']) ?></td><td><?= e($c['slug']) ?></td>
<td>
<form method="post" action="<?= e(url('/admin/danh-muc/xoa')) ?>" class="d-inline" onsubmit="return confirm('Xóa?')">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn-link text-danger p-0">Xóa</button>
</form>
</td></tr>
<?php endforeach; ?>
</tbody></table>
<div class="card p-3">
<h2 class="h6">Thêm</h2>
<form method="post">
<?= csrf_field() ?>
<div class="row g-2">
<div class="col-md-4"><input class="form-control" name="name" placeholder="Tên" required></div>
<div class="col-md-3"><input class="form-control" name="slug" placeholder="slug"></div>
<div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="0"></div>
<div class="col-md-2 form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Hiện</label></div>
</div>
<button class="btn btn-gold mt-2">Tạo</button>
</form>
</div>
