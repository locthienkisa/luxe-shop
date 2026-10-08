<h1 class="h4">Người dùng</h1>
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Email / tên"></div>
<div class="col-md-3"><select name="role_id" class="form-select"><option value="">Role</option><?php foreach ($roles as $ro): ?><option value="<?= (int)$ro['id'] ?>" <?= $role_id==$ro['id']?'selected':'' ?>><?= e($ro['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select name="active" class="form-select"><option value="">Tất cả</option><option value="1" <?= $active===1?'selected':'' ?>>Active</option><option value="0" <?= $active===0?'selected':'' ?>>Khóa</option></select></div>
<div class="col-md-2"><button class="btn btn-outline-secondary">Lọc</button></div>
</form>
<table class="table bg-white"><thead><tr><th>ID</th><th>Email</th><th>Tên</th><th>Role</th><th>TT</th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
<tr><td><?= (int)$u['id'] ?></td><td><a href="<?= e(url('/admin/nguoi-dung/'.$u['id'])) ?>"><?= e($u['email']) ?></a></td><td><?= e($u['full_name']) ?></td><td><?= e($u['role_name']) ?></td><td><?= $u['is_active']?'OK':'Khóa' ?></td></tr>
<?php endforeach; ?>
</tbody></table>
