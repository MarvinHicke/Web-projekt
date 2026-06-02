<article class="col">
    <div class="card h-100 shadow-sm ui-card">

        <img
            src="<?= htmlspecialchars(artworkImageUrl($artwork->getImagefilename(), 'square-small')) ?>"
            class="card-img-top artwork-card-img"
            alt="<?= htmlspecialchars($artwork->getTitle()) ?>"
        >

        <div class="card-body">

            <h2 class="h5 card-title">
                <?= htmlspecialchars($artwork->getTitle()) ?>
            </h2>

            <p class="card-text text-muted">
                <?= htmlspecialchars($artwork->getYearofwork()) ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-artwork.php?id=' . urlencode($artwork->getArtworkid());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

            <?php if (($showAddFavoriteButton ?? false) === true): ?>
                <?php
                $buttonText = 'Zu Favoriten hinzufügen';
                $buttonHref = base_url('pages/add-favorite.php')
                        . '?type=artwork&id=' . urlencode((string) $artwork->getArtworkid())
                        . '&redirect=browse-artworks.php';
                $buttonVariant = 'outline-primary';
                include __DIR__ . '/button.php';
                ?>
            <?php endif; ?>

        </div>
    </div>
</article>