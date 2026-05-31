<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= htmlspecialchars(
                    $artist->getFirstName() . ' ' . $artist->getLastName()
                ) ?>
            </h2>

            <p class="card-text text-muted">
                Artist ID: <?= htmlspecialchars($artist->getId()) ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-artist.php?id=' . urlencode($artist->getArtistid());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>