<?php
require_once __DIR__ . '/../includes/bootstrap.php';
/*
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';
require_once __DIR__ . '/../repositories/galleryRepository.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';
*/
require_once __DIR__ . '/../includes/mock-data.php';

$artworkId = (int) ($_GET['id'] ?? 0);
$selectedArtwork = null;

foreach ($artworks as $artwork) {
    $currentId = (int) ($artwork['id'] ?? $artwork['ArtWorkID'] ?? 0);

    if ($currentId === $artworkId) {
        $selectedArtwork = $artwork;
        break;
    }
}

if ($selectedArtwork === null) {
    $pageTitle = 'Kunstwerk nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <section class="page-heading">
        <h1>Kunstwerk nicht gefunden</h1>
        <p>Die angefragte ID ist ungültig oder das Kunstwerk existiert nicht.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artworks.php')); ?>">Zurück zu Kunstwerke durchsuchen</a>
    </section>

    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$title = (string) ($selectedArtwork['title'] ?? $selectedArtwork['Title'] ?? 'Unbekanntes Kunstwerk');
$artistId = (int) ($selectedArtwork['artist_id'] ?? $selectedArtwork['ArtistID'] ?? 0);
$artistName = trim((string) ($selectedArtwork['artist_first_name'] ?? $selectedArtwork['FirstName'] ?? '') . ' ' . (string) ($selectedArtwork['artist_last_name'] ?? $selectedArtwork['LastName'] ?? ''));
$year = (string) ($selectedArtwork['year'] ?? $selectedArtwork['YearOfWork'] ?? 'Unbekannt');
$image = (string) ($selectedArtwork['image'] ?? $selectedArtwork['ImageFileName'] ?? '');
$largeImage = (string) ($selectedArtwork['large_image'] ?? $image);
$genre = (string) ($selectedArtwork['genre'] ?? 'Nicht hinterlegt');
$subjects = $selectedArtwork['subjects'] ?? [];
$gallery = (string) ($selectedArtwork['gallery'] ?? 'Keine Galerie hinterlegt');
$averageRating = $selectedArtwork['average_rating'] ?? null;

$artworkReviews = [];

foreach ($reviews as $review) {
    $reviewArtworkId = (int) ($review['artwork_id'] ?? $review['ArtWorkId'] ?? 0);

    if ($reviewArtworkId === $artworkId) {
        $artworkReviews[] = $review;
    }
}

$pageTitle = $title . ' · Kunstwerk';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="artwork-detail-layout">
    <div class="artwork-image-panel">
        <img
            src="<?= e($image !== '' ? base_url($image) : artworkImageUrl('', 'large')); ?>"
            alt="<?= e($title); ?>"
        >

        <a href="<?= e($largeImage !== '' ? base_url($largeImage) : artworkImageUrl('', 'large')); ?>" target="_blank" rel="noopener">
            Große Bildversion öffnen
        </a>
    </div>

    <div class="artwork-info-panel">
        <p class="eyebrow">Einzelansicht eines Kunstwerks</p>
        <h1><?= e($title); ?></h1>

        <dl class="detail-list">
            <dt>Künstler</dt>
            <dd>
                <a href="<?= e(artistDetailUrl($artistId)); ?>">
                    <?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?>
                </a>
            </dd>

            <dt>Jahr</dt>
            <dd><?= e($year); ?></dd>

            <dt>Galerie</dt>
            <dd><?= e($gallery); ?></dd>

            <dt>Genre</dt>
            <dd>
                <a class="tag" href="<?= e(base_url('pages/browse-genre.php') . '?name=' . urlencode($genre)); ?>">
                    <?= e($genre); ?>
                </a>
            </dd>

            <dt>Themen</dt>
            <dd>
                <?php if (empty($subjects)): ?>
                    Keine Themen hinterlegt
                <?php endif; ?>

                <?php foreach ($subjects as $subject): ?>
                    <a class="tag" href="<?= e(base_url('pages/browse-subject.php') . '?name=' . urlencode((string) $subject)); ?>">
                        <?= e((string) $subject); ?>
                    </a>
                <?php endforeach; ?>
            </dd>

            <dt>Durchschnittsbewertung</dt>
            <dd>
                <?php if ($averageRating !== null): ?>
                    <?= e(number_format((float) $averageRating, 1, ',', '.')); ?>/5
                <?php else: ?>
                    Noch keine Bewertung
                <?php endif; ?>
            </dd>
        </dl>

        <div class="favorite-box">
            <p><strong>Favorit</strong></p>
            <p>Die Favoritenfunktion wird später mit dem User-Zustand von Team E verbunden.</p>
            <button type="button">Zu Favoriten hinzufügen</button>
        </div>
    </div>
</section>

<section class="reviews-section">
    <h2>Bewertungen</h2>

    <?php if (empty($artworkReviews)): ?>
        <p>Für dieses Kunstwerk gibt es noch keine Bewertungen.</p>
    <?php endif; ?>

    <?php foreach ($artworkReviews as $review): ?>
        <article class="review-card">
            <h3>
                <?= e((string) ($review['user'] ?? 'Unbekannter Nutzer')); ?>
                ·
                <?= e((string) ($review['rating'] ?? '')); ?>/5
            </h3>

            <p><?= e((string) ($review['text'] ?? $review['comment'] ?? '')); ?></p>
            <small><?= e((string) ($review['date'] ?? $review['ReviewDate'] ?? '')); ?></small>
        </article>
    <?php endforeach; ?>
</section>

<section class="sort-panel">
    <h2>Benötigte Datenquellen / Queries für Team A</h2>
    <ul>
        <li>Kunstwerk nach ID: Titel, Jahr, Bilddatei, Galerie, Künstler-ID.</li>
        <li>Künstler nach Künstler-ID für den Künstlerlink.</li>
        <li>Genres und Themen zum Kunstwerk.</li>
        <li>Galerie nach GalleryID, da Gallery statt OriginalHome angezeigt werden soll.</li>
        <li>Bewertungen nach ArtworkID und Durchschnittsbewertung.</li>
        <li>Favoritenstatus später über Session/User-Zustand von Team E.</li>
    </ul>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
