<?php
// Unbekannte Alert-Typen fallen auf die neutrale Info-Darstellung zurück.
$allowedTypes = ['success', 'danger', 'warning', 'info'];
$type = in_array($alertType ?? 'info', $allowedTypes, true)
    ? $alertType
    : 'info';
?>

<div class="alert alert-<?= htmlspecialchars($type) ?>" role="alert">
    <?= htmlspecialchars($alertMessage ?? 'Information') ?>
</div>
