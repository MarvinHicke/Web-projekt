<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Startseite · Art Gallery';

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/repositories/artworkRepository.php';
require_once __DIR__ . '/repositories/artistRepository.php';
require_once __DIR__ . '/repositories/reviewRepository.php';
require_once __DIR__ . '/includes/boxes/top-works-box.php';
require_once __DIR__ . '/includes/boxes/most-reviewed-artists-box.php';
require_once __DIR__ . '/includes/boxes/most-recent-reviews-box.php';

$db = new dbaccess();
$db->connect();

$artworkRepository = new artworkRepository($db);
$artistRepository = new artistRepository($db);
$reviewRepository = new reviewRepository($db);

$topArtworks = $artworkRepository->getTopArtworks(3);
$mostReviewedArtists = $artistRepository->getMostReviewedArtists(3);
$latestReviews = $reviewRepository->getLatestReviewsWithArtwork(3);

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Willkommen bei</p>
        <h1>Art Gallery</h1>
        <p>Entdecken Sie Kunstwerke, Künstler, Bewertungen und Galerien.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artworks.php')); ?>">Kunstwerke durchsuchen</a>
    </div>
</section>

<section class="carousel-placeholder" aria-label="Karussell mit vorgestellten Kunstwerken">
    <h2>Karussell mit vorgestellten Kunstwerken</h2>
    <p>Dieser Bereich ist als Karussell geplant und kann später mit einer UI-Komponente erweitert werden.</p>

    <div class="carousel-strip">
        <?php foreach ($topArtworks as $work): ?>
            <?php
            $title = (string) ($work['Title'] ?? 'Unbekanntes Kunstwerk');
            $imageFileName = (string) ($work['ImageFileName'] ?? '');
            $artworkId = (int) ($work['ArtWorkID'] ?? 0);
            ?>
            <article class="carousel-card">
                <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                    <img src="<?= e(artworkImageUrl($imageFileName, 'square-small')); ?>" alt="<?= e($title); ?>">
                    <h3><?= e($title); ?></h3>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="three-box-grid" aria-label="Datenboxen auf der Startseite">
    <?php renderTopWorksBox($topArtworks); ?>
    <?php renderMostReviewedArtistsBox($mostReviewedArtists); ?>
    <?php renderMostRecentReviewsBox($latestReviews); ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
