<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';
require_once __DIR__ . '/../repositories/galleryRepository.php';

$artworkId = (int) ($_GET['id'] ?? 0);

if ($artworkId <= 0) {
    $pageTitle = 'Kunstwerk nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ungültige ID</h1><p>Bitte eine gültige Artwork-ID angeben.</p>'
       . '<a class="button-link" href="' . e(base_url('pages/browse-artworks.php')) . '">Zurück zur Übersicht</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    $db = new dbaccess();
    $db->connect();

    $artworkObj = (new artworkRepository($db))->getById($artworkId);

    if ($artworkObj === null) {
        $pageTitle = 'Kunstwerk nicht gefunden';
        require_once __DIR__ . '/../includes/header.php';
        echo '<section class="page-heading"><h1>Kunstwerk nicht gefunden</h1>'
           . '<p>Kein Kunstwerk mit dieser ID vorhanden.</p>'
           . '<a class="button-link" href="' . e(base_url('pages/browse-artworks.php')) . '">Zurück zur Übersicht</a></section>';
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    $artistObj       = (new artistRepository($db))->getById($artworkObj->getArtistid());
    $artworkGenres   = (new genreRepository($db))->getGenresForArtwork($artworkId);
    $artworkSubjects = (new subjectRepository($db))->getSubjectsForArtwork($artworkId);
    $galleryObj      = $artworkObj->getGalleryid()
                       ? (new galleryRepository($db))->getById($artworkObj->getGalleryid())
                       : null;
    $reviewRepo      = new reviewRepository($db);
    $ratingData      = $reviewRepo->getAverageRatingArtwork($artworkId);

    $reviewRows = $reviewRepo->getForArtworkWithCustomerData($artworkId);

} catch (Exception $e) {
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ladefehler</h1><p>Das Kunstwerk konnte nicht geladen werden.</p>'
       . '<a class="button-link" href="' . e(base_url('pages/browse-artworks.php')) . '">Zurück</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// ── handle add-review POST (PRG pattern) ─────────────────────────────────────
$reviewErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review']))
{
    if (!isLoggedIn())
    {
        $reviewErrors[] = 'Nur angemeldete Nutzer können Bewertungen abgeben.';
    }
    else
    {
        $loggedInId = (int) ($_SESSION['user']['CustomerID'] ?? 0);
        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $comment = trim(strip_tags((string) ($_POST['comment'] ?? '')));

        if ($loggedInId <= 0)
        {
            $reviewErrors[] = 'Der angemeldete Benutzer konnte nicht erkannt werden.';
        }

        if ($rating === false || $rating < 1 || $rating > 5)
        {
            $reviewErrors[] = 'Bewertung muss zwischen 1 und 5 liegen.';
        }

        if ($comment === '')
        {
            $reviewErrors[] = 'Kommentar darf nicht leer sein.';
        }

        if (empty($reviewErrors))
        {
            if ($reviewRepo->hasUserReviewedArtwork($artworkId, $loggedInId))
            {
                $reviewErrors[] = 'Sie haben dieses Kunstwerk bereits bewertet.';
            }
            else
            {
                $reviewAdded = $reviewRepo->addReview($artworkId, $loggedInId, (int) $rating, $comment);

                if ($reviewAdded)
                {
                    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . $artworkId . '&review=added#reviews');
                    exit;
                }

                $reviewErrors[] = 'Die Bewertung konnte nicht gespeichert werden.';
            }
        }
    }
}

// ── display values ────────────────────────────────────────────────────────────
$title         = $artworkObj->getTitle();
$artistId      = $artworkObj->getArtistid();
$artistName    = $artistObj
                 ? trim($artistObj->getFirstName() . ' ' . $artistObj->getLastName())
                 : 'Unbekannter Künstler';
$year          = (string) ($artworkObj->getYearofwork() ?? '');
$medium        = (string) ($artworkObj->getMedium()     ?? '');
$width         = $artworkObj->getWidth();
$height        = $artworkObj->getHeight();
$description   = cleanHtml($artworkObj->getDescription() ?? $artworkObj->getExcerpt() ?? '');
$imageFileName = $artworkObj->getImagefilename();
$mediumImage   = artworkImageUrl($imageFileName, 'medium');
$largeImage    = artworkImageUrl($imageFileName, 'large');

$dimensions = ($width && $height)
              ? e((string)$width) . ' cm × ' . e((string)$height) . ' cm'
              : '';

$averageRating = (isset($ratingData['AvgRating']) && $ratingData['AvgRating'] !== null)
                 ? round((float) $ratingData['AvgRating'], 1) : null;
$totalReviews  = (int) ($ratingData['TotalReviews'] ?? 0);

$isFavorited = isset($_SESSION['favorites']['artworks'])
               && in_array($artworkId, array_map('intval', $_SESSION['favorites']['artworks']), true);

$loggedInId      = isLoggedIn() ? (int) ($_SESSION['user']['CustomerID'] ?? 0) : 0;
$alreadyReviewed = $loggedInId > 0 && $reviewRepo->hasUserReviewedArtwork($artworkId, $loggedInId);

$pageTitle = $title . ' · Kunstwerk';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ===== BOOTSTRAP MODAL (large image) ===== -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalLabel"><?= e($title); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img src="<?= e($largeImage); ?>" alt="<?= e($title); ?>" class="img-fluid">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== ARTWORK DETAIL ===== -->
<article class="artwork-detail-layout">

    <div class="artwork-image-panel">
        <img
            src="<?= e($mediumImage); ?>"
            alt="<?= e($title); ?>"
            class="artwork-main-image img-fluid"
            role="button"
            data-bs-toggle="modal"
            data-bs-target="#imageModal"
            title="Klicken für große Ansicht"
            style="cursor:zoom-in;"
        >
        <small class="d-block text-muted mt-1">Klicken für große Version</small>
    </div>

    <div class="artwork-info-panel">
        <p class="eyebrow">Kunstwerk</p>
        <h1><?= e($title); ?></h1>
        <p class="artwork-artist-byline">von
            <a href="<?= e(artistDetailUrl($artistId)); ?>"><?= e($artistName); ?></a>
        </p>

        <?php if ($description !== ''): ?>
            <p class="artwork-description"><?= e($description); ?></p>
        <?php endif; ?>

        <!-- Favorite -->
        <?php if ($isFavorited): ?>
            <a class="btn btn-warning btn-sm mb-3"
               href="<?= e(base_url('pages/remove-favorite.php') . '?type=artwork&id=' . urlencode((string) $artworkId) . '&redirect=single-artwork.php'); ?>">
                ★ Aus Favoriten entfernen
            </a>
        <?php else: ?>
            <a class="btn btn-outline-warning btn-sm mb-3"
               href="<?= e(base_url('pages/add-favorite.php') . '?type=artwork&id=' . urlencode((string) $artworkId) . '&redirect=single-artwork.php'); ?>">
                ☆ Zu Favoriten hinzufügen
            </a>
        <?php endif; ?>

        <!-- Details table -->
        <table class="table table-bordered align-middle">
            <caption class="fw-bold text-start pb-2 caption-top">Werkdetails</caption>
            <tbody>
                <tr><th scope="row" style="width:35%">Datum</th>
                    <td><?= $year !== '' ? e($year) : '–'; ?></td></tr>
                <?php if ($medium !== ''): ?>
                <tr><th scope="row">Medium</th>
                    <td><?= e($medium); ?></td></tr>
                <?php endif; ?>
                <?php if ($dimensions !== ''): ?>
                <tr><th scope="row">Abmessungen</th>
                    <td><?= $dimensions; ?></td></tr>
                <?php endif; ?>
                <tr>
                    <th scope="row">Galerie</th>
                    <td>
                        <?php if ($galleryObj): ?>
                            <div class="accordion accordion-flush" id="gallAccordion">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed p-0 bg-transparent shadow-none"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#gallInfo" aria-expanded="false">
                                            <?= e($galleryObj->getGalleryName()); ?>
                                        </button>
                                    </h2>
                                    <div id="gallInfo" class="accordion-collapse collapse">
                                        <div class="accordion-body ps-0 small">
                                            <?php if ($galleryObj->getGalleryNativeName()): ?>
                                                <p class="mb-1"><strong>Einheimischer Name:</strong> <?= e($galleryObj->getGalleryNativeName()); ?></p>
                                            <?php endif; ?>
                                            <?php if ($galleryObj->getGalleryCountry()): ?>
                                                <p class="mb-1"><strong>Land:</strong> <?= e($galleryObj->getGalleryCountry()); ?></p>
                                            <?php endif; ?>
                                            <?php if ($galleryObj->getGalleryWebsite()): ?>
                                                <p class="mb-0"><strong>Website:</strong>
                                                    <a href="<?= e($galleryObj->getGalleryWebsite()); ?>" target="_blank" rel="noopener">
                                                        <?= e($galleryObj->getGalleryWebsite()); ?>
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>–<?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Genres</th>
                    <td>
                        <?php if (empty($artworkGenres)): ?>–
                        <?php else: ?>
                            <?php foreach ($artworkGenres as $g): ?>
                                <a class="badge text-bg-secondary me-1 text-decoration-none"
                                   href="<?= e(genreDetailUrl($g->getGenreID())); ?>">
                                    <?= e($g->getGenreName()); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Themen</th>
                    <td>
                        <?php if (empty($artworkSubjects)): ?>–
                        <?php else: ?>
                            <?php foreach ($artworkSubjects as $s): ?>
                                <a class="badge text-bg-secondary me-1 text-decoration-none"
                                   href="<?= e(subjectDetailUrl($s->getSubjectid())); ?>">
                                    <?= e($s->getSubjectname()); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Durchschnittsbewertung</th>
                    <td>
                        <?php if ($averageRating !== null): ?>
                            <strong><?= e(number_format($averageRating, 1, ',', '.')); ?>/5</strong>
                            <span class="text-muted small">(<?= $totalReviews; ?> Bewertungen)</span>
                        <?php else: ?>
                            <span class="text-muted"></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</article>

<!-- ===== REVIEWS ===== -->
<section id="reviews" class="reviews-section mt-5">
    <h2>Bewertungen</h2>

    <?php if ((string) ($_GET['review'] ?? '') === 'added'): ?>
        <div class="alert alert-success">Ihre Bewertung wurde gespeichert.</div>
    <?php endif; ?>

    <?php if ((string) ($_GET['review'] ?? '') === 'deleted'): ?>
        <div class="alert alert-success">Die Bewertung wurde gelöscht.</div>
    <?php endif; ?>

    <?php if ((string) ($_GET['review'] ?? '') === 'delete-error'): ?>
        <div class="alert alert-danger">Die Bewertung konnte nicht gelöscht werden.</div>
    <?php endif; ?>

    <?php if (empty($reviewRows)): ?>
        <p class="text-muted">Für dieses Kunstwerk gibt es noch keine Bewertungen.</p>
    <?php else: ?>
        <?php foreach ($reviewRows as $row): ?>
            <?php
            $stars = max(0, min(5, (int) ($row['Rating'] ?? 0)));
            $formatted = !empty($row['ReviewDate']) ? date('d.m.Y', strtotime($row['ReviewDate'])) : '';
            $city = trim((string) ($row['City'] ?? ''));
            $country = trim((string) ($row['Country'] ?? ''));
            $reviewerName = trim((string) ($row['ReviewerFirstName'] ?? '') . ' ' . (string) ($row['ReviewerLastName'] ?? ''));

            if ($reviewerName === '')
            {
                $reviewerName = 'Unbekannter Reviewer';
            }

            $reviewerLocation = 'Ort unbekannt';

            if ($city !== '' && $country !== '')
            {
                $reviewerLocation = $city . ' (' . $country . ')';
            }
            elseif ($city !== '')
            {
                $reviewerLocation = $city;
            }
            elseif ($country !== '')
            {
                $reviewerLocation = $country;
            }

            $isOwn = $loggedInId > 0 && (int) ($row['CustomerId'] ?? -1) === $loggedInId;
            ?>
            <article class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="text-warning fs-5"><?= str_repeat('★', $stars) . str_repeat('☆', 5 - $stars); ?></span>
                            <strong class="ms-2"><?= e($reviewerName); ?></strong>
                            <span class="text-muted ms-2 small"><?= e($reviewerLocation); ?></span>
                            <?php if ($isOwn): ?>
                                <span class="badge text-bg-info ms-1">Deine Bewertung</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= e($formatted); ?></small>
                    </div>
                    <p class="mt-2 mb-1"><?= e(cleanHtml((string)($row['Comment'] ?? ''))); ?></p>
                    <?php if (isAdmin()): ?>
                        <form method="post" action="<?= e(base_url('pages/delete-review.php')); ?>"
                              onsubmit="return confirm('Bewertung wirklich löschen?');">
                            <input type="hidden" name="review_id"  value="<?= (int)($row['ReviewId'] ?? 0); ?>">
                            <input type="hidden" name="artwork_id" value="<?= $artworkId; ?>">
                            <button type="submit" class="btn btn-danger btn-sm mt-1">Bewertung löschen</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Add review form -->
    <?php if (isLoggedIn() && !$alreadyReviewed): ?>
        <div class="card mt-4">
            <div class="card-body">
                <h3 class="h5">Bewertung hinzufügen</h3>
                <?php foreach ($reviewErrors as $err): ?>
                    <div class="alert alert-danger py-2"><?= e($err); ?></div>
                <?php endforeach; ?>
                <form method="post" action="<?= e(base_url('pages/single-artwork.php') . '?id=' . $artworkId); ?>">
                    <input type="hidden" name="add_review" value="1">
                    <div class="mb-3">
                        <label for="rating" class="form-label fw-bold">Bewertung (1–5)</label>
                        <input type="number" id="rating" name="rating" class="form-control"
                               min="1" max="5" required value="<?= (int)($_POST['rating'] ?? 3); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-bold">Kommentar</label>
                        <textarea
                                id="comment"
                                name="comment"
                                class="form-control"
                                rows="4"
                                required
                        ><?= e((string) ($_POST['comment'] ?? '')); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Bewertung speichern</button>
                </form>
            </div>
        </div>
    <?php elseif (isLoggedIn() && $alreadyReviewed): ?>
        <p class="text-muted mt-3">Sie haben dieses Kunstwerk bereits bewertet.</p>
    <?php else: ?>
        <p class="mt-3 small">
            <a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a>, um eine Bewertung zu hinterlassen.
        </p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
