<?php
// Der Titel wird escaped; $accordionContent darf vorbereitetes Komponenten-HTML enthalten.
?>
<div class="accordion-item">

    <button class="accordion-toggle">
        <?= htmlspecialchars($accordionTitle ?? 'Accordion Title') ?>
    </button>

    <div class="accordion-content">
        <?= $accordionContent ?? '' ?>
    </div>

</div>
