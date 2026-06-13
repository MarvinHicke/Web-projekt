<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <a href="<?= e(artistDetailUrl((int) $artist->getId())); ?>">
            <?php // Der zentrale Helper liefert bei fehlendem Künstlerbild den Placeholder. ?>
            <img
                src="<?= e(artistImageUrl((int) $artist->getId(), 'square-medium')); ?>"
                class="card-img-top entity-card-img"
                alt="<?= e($artist->getFirstName() . ' ' . $artist->getLastName()); ?>"
            >
        </a>
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= e(
                    $artist->getFirstName() . ' ' . $artist->getLastName()
                ); ?>
            </h2>

            <p class="card-text text-muted">
                Künstler-ID: <?= e((string) $artist->getId()); ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-artist.php?id=' . urlencode($artist->getId());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>
            <?php if (($showAddFavoriteButton ?? false) === true): ?>
                <?php
                $buttonText = 'Zu Favoriten hinzufügen';
                $buttonHref = base_url('pages/add-favorite.php')
                        . '?type=artist&id=' . urlencode((string) $artist->getId())
                        . '&redirect=browse-artists.php';
                $buttonVariant = 'outline-primary';
                include __DIR__ . '/button.php';
                ?>
            <?php endif; ?>

            <?php if (($showRemoveFavoriteButton ?? false) === true): ?>
                <div class="mt-2">
                    <?php
                    $buttonText = 'Aus Favoriten entfernen';
                    $buttonHref = base_url('pages/remove-favorite.php')
                            . '?type=artist&id=' . urlencode((string) $artist->getId());
                    $buttonVariant = 'danger';
                    include __DIR__ . '/button.php';
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</article>
