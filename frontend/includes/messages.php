<?php
require_once __DIR__ . '/../../core/Request.php';

$request = new Request();
?>

<?php if ($request->query('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center w-100 position-relative" role="alert">
        <?php echo $request->query('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if ($request->query('success')): ?>
    <div class="alert alert-success alert-dismissible fade show text-center w-100 position-relative" role="alert">
        <?php echo $request->query('success_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>
