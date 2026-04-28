<?php
$pageTitle = 'Home · Art Gallery';

require_once __DIR__ . '/includes/mock-data.php'; /* later should be edited by A with the real data*/
require_once __DIR__ . '/includes/boxes/top-works-box.php';
require_once __DIR__ . '/includes/boxes/most-reviewed-artists-box.php';
require_once __DIR__ . '/includes/boxes/most-recent-reviews-box.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Welcome to</p>
        <h1>Art Gallery</h1>
        <p>Discover artworks, artists, reviews and galleries.</p>
        <a class="button-link" href="browse-artworks.php">Browse Artworks</a>
    </div>
</section>

<section class="carousel-placeholder" aria-label="Featured artworks carousel placeholder">
    <h2>Featured artworks carousel</h2>
    <p>This area is planned as a carousel. In Sprint 1 it is only a layout placeholder.</p>

    <div class="carousel-strip">
        <?php foreach (array_slice($artworks, 0, 3) as $work): ?>
            <article class="carousel-card">
                <img src="<?= e((string) $work['image']); ?>" alt="<?= e((string) $work['title']); ?>">
                <h3><?= e((string) $work['title']); ?></h3>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="three-box-grid" aria-label="Homepage data boxes">
    <?php renderTopWorksBox($artworks); ?>
    <?php renderMostReviewedArtistsBox($artists); ?>
    <?php renderMostRecentReviewsBox($reviews); ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
