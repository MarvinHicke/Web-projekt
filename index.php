<?php
$pageTitle = 'Startseite · Art Gallery';

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/repositories/artworkRepository.php';
require_once __DIR__ . '/repositories/artistRepository.php';
require_once __DIR__ . '/repositories/reviewRepository.php';
require_once __DIR__ . '/includes/boxes/top-works-box.php';
require_once __DIR__ . '/includes/boxes/most-reviewed-artists-box.php';
require_once __DIR__ . '/includes/boxes/most-recent-reviews-box.php';

$topArtworks         = [];
$mostReviewedArtists = [];
$latestReviews       = [];

try {
    $db = new dbaccess();
    $db->connect();

    $artworkRepo = new artworkRepository($db);
    $artistRepo  = new artistRepository($db);
    $reviewRepo  = new reviewRepository($db);

    $topArtworks         = $artworkRepo->getTopArtworks(5);
    $mostReviewedArtists = $artistRepo->getMostReviewedArtists(3);
    // Use the new method that returns raw arrays with ArtworkTitle
    $latestReviews       = $reviewRepo->getLatestReviewsWithDetails(3);
} catch (Exception $e) {
    // DB not available — widgets show empty state
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO ===== -->
<section class="hero">
    <div>
        <p class="eyebrow">Willkommen bei</p>
        <h1>Art Gallery</h1>
        <p>Entdecken Sie Kunstwerke, Künstler, Bewertungen und Galerien.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artworks.php')); ?>">Kunstwerke durchsuchen</a>
    </div>
</section>

<!-- ===== BOOTSTRAP CAROUSEL ===== -->
<?php if (!empty($topArtworks)): ?>
<section class="carousel-section" aria-label="Vorgestellte Kunstwerke">
    <div id="artworkCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

        <div class="carousel-indicators">
            <?php foreach ($topArtworks as $i => $work): ?>
                <button
                    type="button"
                    data-bs-target="#artworkCarousel"
                    data-bs-slide-to="<?= $i; ?>"
                    <?= $i === 0 ? 'class="active" aria-current="true"' : ''; ?>
                    aria-label="Slide <?= $i + 1; ?>"
                ></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($topArtworks as $i => $work): ?>
                <?php
                $id            = (int)    ($work['ArtWorkID']    ?? 0);
                $title         = (string) ($work['Title']        ?? 'Unbekanntes Kunstwerk');
                $imageFileName = (string) ($work['ImageFileName'] ?? '');
                $rating        = $work['AvgRating'] ?? null;
                ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                    <a href="<?= e(artworkDetailUrl($id)); ?>">
                        <img
                            src="<?= e(artworkImageUrl($imageFileName, 'large')); ?>"
                            class="d-block w-100 carousel-img"
                            alt="<?= e($title); ?>"
                            style="max-height:520px; object-fit:cover;"
                        >
                    </a>
                    <div class="carousel-caption d-none d-md-block">
                        <h5><?= e($title); ?></h5>
                        <?php if ($rating !== null): ?>
                            <p>Bewertung: <?= e(number_format((float)$rating, 1, ',', '.')); ?>/5</p>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-light mt-1" href="<?= e(artworkDetailUrl($id)); ?>">Ansehen</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#artworkCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Zurück</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#artworkCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Weiter</span>
        </button>
    </div>
</section>
<?php endif; ?>

<!-- ===== THREE DATA BOXES ===== -->
<section class="three-box-grid" aria-label="Datenboxen auf der Startseite">
    <?php renderTopWorksBox($topArtworks); ?>
    <?php renderMostReviewedArtistsBox($mostReviewedArtists); ?>
    <?php renderMostRecentReviewsBox($latestReviews); ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
