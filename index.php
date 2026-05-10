<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Startseite · Art Gallery';

require_once __DIR__ . '/includes/mock-data.php'; /* später durch echte Daten von A ersetzen */
require_once __DIR__ . '/includes/boxes/top-works-box.php';
require_once __DIR__ . '/includes/boxes/most-reviewed-artists-box.php';
require_once __DIR__ . '/includes/boxes/most-recent-reviews-box.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Willkommen bei</p>
        <h1>Art Gallery</h1>
        <p>Entdecken Sie Kunstwerke, Künstler, Rezensionen und Galerien.</p>
        <a class="button-link" href="browse-artworks.php">Kunstwerke durchsuchen</a>
    </div>
</section>

<section class="carousel-placeholder" aria-label="Karussell mit vorgestellten Kunstwerken">
    <h2>Karussell mit vorgestellten Kunstwerken</h2>
    <p>Dieser Bereich ist als Karussell geplant. In Sprint 1 dient er nur als Layout-Platzhalter.</p>

    <div class="carousel-strip">
        <?php foreach (array_slice($artworks, 0, 3) as $work): ?>
            <article class="carousel-card">
                <img src="<?= e((string) $work['image']); ?>" alt="<?= e((string) $work['title']); ?>">
                <h3><?= e((string) $work['title']); ?></h3>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="three-box-grid" aria-label="Datenboxen auf der Startseite">
    <?php renderTopWorksBox($artworks); ?>
    <?php renderMostReviewedArtistsBox($artists); ?>
    <?php renderMostRecentReviewsBox($reviews); ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>