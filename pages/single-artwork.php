<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';
require_once __DIR__ . '/../repositories/galleryRepository.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';

$artworkId = (int) ($_GET['id'] ?? 0);

$db = new dbaccess();
$db->connect();

$artworkRepository = new artworkRepository($db);
$artistRepository = new artistRepository($db);
$genreRepository = new genreRepository($db);
$subjectRepository = new subjectRepository($db);
$galleryRepository = new galleryRepository($db);
$reviewRepository = new reviewRepository($db);

$artwork = $artworkId > 0 ? $artworkRepository->getById($artworkId) : null;

if ($artwork === null) {
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

$artist = $artistRepository->getById((int) $artwork->getArtistid());
$genres = $genreRepository->getGenresForArtwork($artworkId);
$subjects = $subjectRepository->getSubjectsForArtwork($artworkId);
$gallery = $artwork->getGalleryid() ? $galleryRepository->getById((int) $artwork->getGalleryid()) : null;
$reviews = $reviewRepository->getForArtwork($artworkId);
$ratingInfo = $reviewRepository->getAverageRatingArtwork($artworkId);

$averageRating = $ratingInfo['AvgRating'] ?? null;
$totalReviews = $ratingInfo['TotalReviews'] ?? count($reviews);

$pageTitle = $artwork->getTitle() . ' · Kunstwerk';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="artwork-detail-layout">
    <div class="artwork-image-panel">
        <img
            src="<?= e(artworkImageUrl($artwork->getImagefilename(), 'large')); ?>"
            alt="<?= e($artwork->getTitle()); ?>"
        >

        <a href="<?= e(artworkImageUrl($artwork->getImagefilename(), 'large')); ?>" target="_blank" rel="noopener">
            Große Bildversion öffnen
        </a>
    </div>

    <div class="artwork-info-panel">
        <p class="eyebrow">Einzelansicht eines Kunstwerks</p>
        <h1><?= e($artwork->getTitle()); ?></h1>

        <dl class="detail-list">
            <dt>Künstler</dt>
            <dd>
                <?php if ($artist !== null): ?>
                    <a href="<?= e(artistDetailUrl((int) $artist->getId())); ?>">
                        <?= e($artist->getFirstName() . ' ' . $artist->getLastName()); ?>
                    </a>
                <?php else: ?>
                    Unbekannt
                <?php endif; ?>
            </dd>

            <dt>Jahr</dt>
            <dd><?= e((string) $artwork->getYearofwork()); ?></dd>

            <dt>Typ</dt>
            <dd><?= e((string) $artwork->getArtworktype()); ?></dd>

            <dt>Medium</dt>
            <dd><?= e((string) $artwork->getMedium()); ?></dd>

            <dt>Größe</dt>
            <dd><?= e((string) $artwork->getWidth()); ?> × <?= e((string) $artwork->getHeight()); ?></dd>

            <dt>Galerie</dt>
            <dd>
                <?php if ($gallery !== null): ?>
                    <?= e($gallery->getGalleryName()); ?>
                    <?php if ($gallery->getGalleryCountry()): ?>
                        <small><?= e($gallery->getGalleryCountry()); ?></small>
                    <?php endif; ?>
                <?php else: ?>
                    Keine Galerie hinterlegt
                <?php endif; ?>
            </dd>

            <dt>Genre</dt>
            <dd>
                <?php if (empty($genres)): ?>
                    Keine Genres hinterlegt
                <?php endif; ?>

                <?php foreach ($genres as $genre): ?>
                    <a class="tag" href="<?= e(base_url('pages/browse-genre.php') . '?id=' . urlencode((string) $genre->getGenreID())); ?>">
                        <?= e($genre->getGenreName()); ?>
                    </a>
                <?php endforeach; ?>
            </dd>

            <dt>Themen</dt>
            <dd>
                <?php if (empty($subjects)): ?>
                    Keine Themen hinterlegt
                <?php endif; ?>

                <?php foreach ($subjects as $subject): ?>
                    <a class="tag" href="<?= e(base_url('pages/browse-subject.php') . '?id=' . urlencode((string) $subject->getSubjectid())); ?>">
                        <?= e($subject->getSubjectname()); ?>
                    </a>
                <?php endforeach; ?>
            </dd>

            <dt>Durchschnittsbewertung</dt>
            <dd>
                <?php if ($averageRating !== null): ?>
                    <?= e(number_format((float) $averageRating, 1, ',', '.')); ?>/5
                    <small>(<?= e((string) $totalReviews); ?> Bewertungen)</small>
                <?php else: ?>
                    Noch keine Bewertung
                <?php endif; ?>
            </dd>
        </dl>

        <div class="favorite-box">
            <p><strong>Favorit</strong></p>
            <p>Die Favoritenfunktion wird später mit dem User-Zustand verbunden.</p>
            <a class="button-link" href="<?= e(base_url('pages/add-favorite.php') . '?type=artwork&id=' . urlencode((string) $artworkId)); ?>">
                Zu Favoriten hinzufügen
            </a>
        </div>
    </div>
</section>

<section class="reviews-section">
    <h2>Bewertungen</h2>

    <?php if (empty($reviews)): ?>
        <p>Für dieses Kunstwerk gibt es noch keine Bewertungen.</p>
    <?php endif; ?>

    <?php foreach ($reviews as $review): ?>
        <article class="review-card">
            <h3>Bewertung: <?= e((string) $review->getRating()); ?>/5</h3>
            <p><?= e($review->getComment()); ?></p>
            <small><?= e($review->getReviewDateFormatted()); ?></small>
        </article>
    <?php endforeach; ?>
</section>

<section class="sort-panel">
    <h2>Benötigte Datenquellen / Queries für Team A</h2>
    <ul>
        <li>Kunstwerk nach ID: Titel, Jahr, Bilddatei, Typ, Medium, Größe, Künstler-ID und Galerie-ID.</li>
        <li>Künstler nach Künstler-ID für den Künstlerlink.</li>
        <li>Genres über die N:M-Beziehung ArtworkGenres.</li>
        <li>Themen über die N:M-Beziehung ArtworkSubjects.</li>
        <li>Galerie nach GalleryID, da Gallery statt OriginalHome angezeigt werden soll.</li>
        <li>Bewertungen nach ArtworkID und Durchschnittsbewertung aus Reviews.</li>
        <li>Favoritenstatus später über Session/User-Zustand von Team E.</li>
    </ul>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
