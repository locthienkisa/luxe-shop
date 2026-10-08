<h1 class="h4">Đánh giá</h1>
<form class="mb-3"><select name="status" class="form-select d-inline w-auto" onchange="this.form.submit()">
<option value="">Tất cả</option>
<?php foreach (['pending','approved','hidden'] as $s): ?><option <?= $status===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
</select></form>
<table class="table bg-white"><tbody>
<?php foreach ($reviews as $rv): ?>
<tr>
<td>#<?= (int)$rv['id'] ?></td>
<td><?= e($rv['product_name']) ?></td>
<td><?= e($rv['email']) ?></td>
<td><?= (int)$rv['rating'] ?>★</td>
<td><?= e($rv['content']) ?></td>
<td><?= e($rv['status']) ?></td>
<td>
<form method="post" action="<?= e(url('/admin/danh-gia/duyet')) ?>" class="d-inline"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= (int)$rv['id'] ?>">
<button name="status" value="approved" class="btn btn-sm btn-success">Duyệt</button>
<button name="status" value="hidden" class="btn btn-sm btn-secondary">Ẩn</button>
</form>
<form method="post" action="<?= e(url('/admin/danh-gia/xoa')) ?>" class="d-inline"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= (int)$rv['id'] ?>"><button class="btn btn-sm btn-danger">Xóa</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody></table>
