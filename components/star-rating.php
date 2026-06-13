<?php
// Ungültige Werte werden vor der Ausgabe auf die sichtbare Skala von 0 bis 5 begrenzt.
$rating = max(0, min(5, (int) ($rating ?? 0)));
?>

<div class="star-rating">

    <?php for ($i = 1; $i <= 5; $i++): ?>

        <?php if ($i <= $rating): ?>
            <span class="star filled">★</span>
        <?php else: ?>
            <span class="star">☆</span>
        <?php endif; ?>

    <?php endfor; ?>

</div>
