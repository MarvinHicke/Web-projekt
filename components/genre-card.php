<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= htmlspecialchars(
                    $genre->getAllForBrowse()
                ) ?>
            </h2>

            <p class="card-text text-muted">
                Genre ID: <?= htmlspecialchars($genre->getId()) ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-genre.php?id=' . urlencode($genre->getGenreid());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>