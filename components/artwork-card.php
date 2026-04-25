<article class="col">
    <div class="card h-100 shadow-sm ui-card">

        <img
            src="../images/works/square-small/<?= htmlspecialchars($artwork->getImagefilename()) ?>.jpg"
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
                $buttonText = 'View Artwork';
                $buttonHref = 'single-artwork.php?id=' . urlencode($artwork->getArtworkid());
                $buttonVariant = 'primary';
                include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>