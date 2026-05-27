<?php
$allowedTypes = ['success', 'danger', 'warning', 'info'];
$type = in_array($alertType ?? 'info', $allowedTypes, true)
    ? $alertType
    : 'info';
?>

<div class="alert alert-<?= htmlspecialchars($type) ?>" role="alert">
    <?= htmlspecialchars($alertMessage ?? 'Information') ?>
</div>