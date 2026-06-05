<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= htmlspecialchars($genre->getGenreName()) ?>
            </h2>

            <p class="card-text text-muted">
                Epoche: <?= htmlspecialchars($genre->getEra()) ?>
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