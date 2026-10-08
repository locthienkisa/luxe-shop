<div class="d-flex justify-content-between mb-3"><h1 class="h4">Sản phẩm</h1><a class="btn btn-gold" href="<?= e(url('/admin/san-pham/them')) ?>">Thêm</a></div>
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="<?= e($filters['q']??'') ?>" placeholder="Tên / SKU"></div>
<div class="col-md-2"><button class="btn btn-outline-secondary">Tìm</button></div>
</form>
<table class="table bg-white">
<thead><tr><th>ID</th><th>Tên</th><th>SKU</th><th>Giá</th><th>Kho</th><th>TT</th><th></th></tr></thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr>
<td><?= (int)$p['id'] ?></td>
<td><?= e($p['name']) ?></td>
<td><?= e($p['sku']) ?></td>
<td><?= money($p['price']) ?></td>
<td><?= (int)$p['stock'] ?></td>
<td><?= $p['is_active']?'Hiện':'Ẩn' ?></td>
<td>
<a href="<?= e(url('/admin/san-pham/'.$p['id'].'/sua')) ?>">Sửa</a>
<form class="d-inline" method="post" action="<?= e(url('/admin/san-pham/xoa')) ?>" onsubmit="return confirm('Xóa?')">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="btn btn-link text-danger p-0">Xóa</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
