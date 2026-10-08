<h1 class="h4">Banner</h1>
<table class="table bg-white"><?php foreach ($banners as $b): ?>
<tr><td><?= e($b['title']) ?></td><td><?= e($b['position']) ?></td>
<td><form method="post" action="<?= e(url('/admin/banner/xoa')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn btn-link text-danger">Xóa</button></form></td>
</tr><?php endforeach; ?></table>
<form method="post" enctype="multipart/form-data" class="card p-3">
<?= csrf_field() ?>
<div class="row g-2">
<div class="col-md-4"><input class="form-control" name="title" placeholder="Tiêu đề" required></div>
<div class="col-md-4"><input class="form-control" name="subtitle" placeholder="Phụ đề"></div>
<div class="col-md-4"><input class="form-control" name="link" placeholder="Link"></div>
<div class="col-md-3"><select name="position" class="form-select"><option>hero</option><option>promo</option><option>sidebar</option></select></div>
<div class="col-md-3"><input class="form-control" type="file" name="image" accept="image/*" required></div>
<div class="col-md-2 form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Hiện</div>
</div>
<button class="btn btn-gold mt-2">Tạo</button>
</form>
