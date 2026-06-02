<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= htmlspecialchars(
                    $subject->getAllForBrowse()
                ) ?>
            </h2>

            <p class="card-text text-muted">
                Subject ID: <?= htmlspecialchars($subject->getId()) ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-subject.php?id=' . urlencode($subject->getArtistid());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>