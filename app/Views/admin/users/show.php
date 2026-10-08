<h1 class="h4"><?= e($user['full_name']) ?></h1>
<p><?= e($user['email']) ?> · <?= e($user['role_name']) ?> · <?= $user['is_active']?'Hoạt động':'Đã khóa' ?></p>
<form method="post" action="<?= e(url('/admin/nguoi-dung/khoa')) ?>" class="d-inline">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
<input type="hidden" name="is_active" value="<?= $user['is_active']?0:1 ?>">
<button class="btn btn-outline-warning"><?= $user['is_active']?'Khóa':'Mở khóa' ?></button>
</form>
<?php if (\App\Core\Auth::hasRole('admin','super_admin')): ?>
<form method="post" action="<?= e(url('/admin/nguoi-dung/vai-tro')) ?>" class="d-inline">
<?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
<select name="role_id"><?php foreach (\App\Models\User::db()->query('SELECT * FROM roles')->fetchAll() as $ro): ?>
<option value="<?= (int)$ro['id'] ?>" <?= $ro['id']==$user['role_id']?'selected':'' ?>><?= e($ro['name']) ?></option>
<?php endforeach; ?></select>
<button class="btn btn-outline-dark">Đổi role</button>
</form>
<?php endif; ?>
<h2 class="h6 mt-4">Đơn hàng</h2>
<ul><?php foreach ($orders as $o): ?><li><a href="<?= e(url('/admin/don-hang/'.$o['id'])) ?>"><?= e($o['order_number']) ?></a> — <?= money($o['total']) ?> — <?= e(order_status_label($o['status'])) ?></li><?php endforeach; ?></ul>
