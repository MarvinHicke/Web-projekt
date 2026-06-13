<?php
// Nur freigegebene Bootstrap-Varianten werden als CSS-Klasse ausgegeben.
$allowedVariants = ['primary', 'secondary', 'danger', 'outline-primary'];
$variant = in_array($buttonVariant ?? 'primary', $allowedVariants, true)
    ? $buttonVariant
    : 'primary';
?>

<a
    href="<?= htmlspecialchars($buttonHref ?? '#') ?>"
    class="btn btn-<?= htmlspecialchars($variant) ?>"
>
    <?= htmlspecialchars($buttonText ?? 'öffnen') ?>
</a>
