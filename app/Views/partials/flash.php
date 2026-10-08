<?php
$success = flash('success');
$error = flash('error');
$errors = flash('errors') ?: [];
\App\Core\Session::forget('_old');
?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
