<?php
// Englische und ältere deutsche Variablennamen bleiben für bestehende Includes kompatibel.
$seitenTitel = $title ?? $titel ?? 'Seitentitel';
$seitenUntertitel = $subtitle ?? $untertitel ?? '';
?>

<section class="page-header mb-4">
    <h1 class="page-title">
        <?= htmlspecialchars($seitenTitel) ?>
    </h1>

    <?php if (!empty($seitenUntertitel)): ?>
        <p class="page-subtitle">
            <?= htmlspecialchars($seitenUntertitel) ?>
        </p>
    <?php endif; ?>
</section>
