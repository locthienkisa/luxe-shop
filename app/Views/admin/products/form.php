<?php $p = $product; $action = $p ? url('/admin/san-pham/'.$p['id']) : url('/admin/san-pham'); ?>
<h1 class="h4"><?= e($title) ?></h1>
<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="card p-3">
<?= csrf_field() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tên</label><input class="form-control" name="name" required value="<?= e($p['name']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= e($p['slug']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">SKU</label><input class="form-control" name="sku" required value="<?= e($p['sku']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Thương hiệu</label><input class="form-control" name="brand" value="<?= e($p['brand']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Giá</label><input class="form-control" type="number" step="0.01" name="price" required value="<?= e($p['price']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Giá KM</label><input class="form-control" type="number" step="0.01" name="sale_price" value="<?= e($p['sale_price']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Tồn kho</label><input class="form-control" type="number" name="stock" value="<?= e($p['stock']??0) ?>"></div>
<div class="col-12"><label class="form-label">Mô tả ngắn</label><input class="form-control" name="short_description" value="<?= e($p['short_description']??'') ?>"></div>
<div class="col-12"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="5"><?= e($p['description']??'') ?></textarea></div>
<div class="col-12">
<label class="form-label">Danh mục</label>
<div class="d-flex flex-wrap gap-3">
<?php foreach ($categories as $c): ?>
<label><input type="checkbox" name="categories[]" value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $selected??[], true)?'checked':'' ?>> <?= e($c['name']) ?></label>
<?php endforeach; ?>
</div>
</div>
<div class="col-md-4"><label class="form-label">Ảnh</label><input class="form-control" type="file" name="image" accept="image/*"></div>
<div class="col-md-4"><label class="form-label">Meta title</label><input class="form-control" name="meta_title" value="<?= e($p['meta_title']??'') ?>"></div>
<div class="col-md-4"><label class="form-label">Meta desc</label><input class="form-control" name="meta_description" value="<?= e($p['meta_description']??'') ?>"></div>
<div class="col-12">
<label class="me-3"><input type="checkbox" name="is_featured" value="1" <?= !empty($p['is_featured'])?'checked':'' ?>> Nổi bật</label>
<label class="me-3"><input type="checkbox" name="is_active" value="1" <?= !isset($p['is_active']) || $p['is_active']?'checked':'' ?>> Hiện</label>
<label><input type="checkbox" name="has_variants" value="1" <?= !empty($p['has_variants'])?'checked':'' ?>> Có biến thể</label>
</div>
</div>
<button class="btn btn-gold mt-3">Lưu</button>
</form>
