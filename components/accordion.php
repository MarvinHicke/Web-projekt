<div class="accordion-item">

    <button class="accordion-toggle">
        <?= htmlspecialchars($accordionTitle ?? 'Accordion Title') ?>
    </button>

    <div class="accordion-content">
        <?= $accordionContent ?? '' ?>
    </div>

</div>