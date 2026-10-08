<h1 class="h4">Cài đặt</h1>
<form method="post" class="card p-3" style="max-width:640px">
<?= csrf_field() ?>
<?php foreach (['site_name'=>'Tên site','meta_description'=>'Meta description','hotline'=>'Hotline','address'=>'Địa chỉ','email_contact'=>'Email','low_stock_threshold'=>'Ngưỡng tồn thấp'] as $k=>$lab): ?>
<div class="mb-3"><label class="form-label"><?= e($lab) ?></label>
<input class="form-control" name="<?= e($k) ?>" value="<?= e($settings[$k] ?? '') ?>">
</div>
<?php endforeach; ?>
<button class="btn btn-gold">Lưu</button>
</form>
