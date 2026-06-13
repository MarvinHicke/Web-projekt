<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <?php // Genre-Bilder werden über den gemeinsamen Fallback-Helper aufgelöst. ?>
        <img
            src="<?= e(genreImageUrl((int) $genre->getGenreID())); ?>"
            class="card-img-top entity-card-img"
            alt="<?= e($genre->getGenreName()); ?>"
        >
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= e($genre->getGenreName()); ?>
            </h2>

            <p class="card-text text-muted">
                Epoche: <?= e((string) $genre->getEra()); ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-genre.php?id=' . urlencode($genre->getGenreID());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>
