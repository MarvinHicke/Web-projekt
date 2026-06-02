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

        </div>
    </div>
</article>