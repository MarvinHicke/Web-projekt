
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
 
// ── helper to render error and exit ──────────────────────────────────────────
function renderError(string $msg, string $backUrl): void
{
    global $pageTitle;
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Fehler</h1><p>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</p>'
       . '<a class="button-link" href="' . htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') . '">Zurück zur Übersicht</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
 
if ($artworkId <= 0) {
    renderError('Ungültige Kunstwerk-ID.', base_url('pages/browse-artworks.php'));
}
 
// ── load data ─────────────────────────────────────────────────────────────────
try {
    $db = new dbaccess();
    $db->connect();
 
    $artworkRepo = new artworkRepository($db);
    $artworkObj  = $artworkRepo->getById($artworkId);
 
    if ($artworkObj === null) {
        renderError('Kein Kunstwerk mit dieser ID gefunden.', base_url('pages/browse-artworks.php'));
    }
 
    $artistRepo  = new artistRepository($db);
    $genreRepo   = new genreRepository($db);
    $subjectRepo = new subjectRepository($db);
    $reviewRepo  = new reviewRepository($db);
    $galleryRepo = new galleryRepository($db);
 
    $artistObj       = $artistRepo->getById($artworkObj->getArtistid());
    $artworkGenres   = $genreRepo->getGenresForArtwork($artworkId);
    $artworkSubjects = $subjectRepo->getSubjectsForArtwork($artworkId);
    $galleryObj      = $artworkObj->getGalleryid()
                       ? $galleryRepo->getById($artworkObj->getGalleryid())
                       : null;
 
    // reviews with customer info (city + country) via JOIN
    $reviewSql  = "SELECT r.ReviewId, r.Rating, r.Comment, r.ReviewDate,
                          c.City, c.Country, cl.UserName
                   FROM reviews r
                   LEFT JOIN customers c  ON r.CustomerId = c.CustomerID
                   LEFT JOIN customerlogon cl ON r.CustomerId = cl.CustomerID
                   WHERE r.ArtWorkId = :id
                   ORDER BY r.ReviewDate DESC";
    $reviewStmt = $db->preparedStatement($reviewSql);
    $reviewStmt->execute(['id' => $artworkId]);
    $reviewRows = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
 
    $ratingData    = $reviewRepo->getAverageRatingArtwork($artworkId);
    $averageRating = (isset($ratingData['AvgRating']) && $ratingData['AvgRating'] !== null)
                     ? round((float) $ratingData['AvgRating'], 1) : null;
    $totalReviews  = (int) ($ratingData['TotalReviews'] ?? 0);
 
} catch (Exception $e) {
    renderError('Das Kunstwerk konnte nicht geladen werden.', base_url('pages/browse-artworks.php'));
}
 
// ── handle add-review POST ────────────────────────────────────────────────────
$reviewErrors  = [];
$reviewSuccess = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!isLoggedIn()) {
        $reviewErrors[] = 'Nur angemeldete Nutzer können Bewertungen abgeben.';
    } else {
        $loggedInCustomerId = (int) ($_SESSION['user']['CustomerID'] ?? 0);
        $rating  = (int) ($_POST['rating'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
 
        if ($rating < 1 || $rating > 5) {
            $reviewErrors[] = 'Bitte wählen Sie eine Bewertung zwischen 1 und 5.';
        }
        if ($comment === '') {
            $reviewErrors[] = 'Der Kommentar darf nicht leer sein.';
        }
        if (empty($reviewErrors)) {
            try {
                if ($reviewRepo->hasUserReviewedArtwork($artworkId, $loggedInCustomerId)) {
                    $reviewErrors[] = 'Sie haben dieses Kunstwerk bereits bewertet.';
                } else {
                    $reviewRepo->addReview($artworkId, $loggedInCustomerId, $rating, $comment);
                    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . $artworkId . '&reviewed=1');
                    exit;
                }
            } catch (Exception $e) {
                $reviewErrors[] = 'Fehler beim Speichern der Bewertung.';
            }
        }
    }
}
 
// ── computed display values ───────────────────────────────────────────────────
$title         = $artworkObj->getTitle();
$artistId      = $artworkObj->getArtistid();
$artistName    = $artistObj
                 ? trim($artistObj->getFirstName() . ' ' . $artistObj->getLastName())
                 : 'Unbekannter Künstler';
$year          = (string) ($artworkObj->getYearofwork() ?? '');
$medium        = (string) ($artworkObj->getMedium() ?? '');
$width         = $artworkObj->getWidth();
$height        = $artworkObj->getHeight();
$description   = (string) ($artworkObj->getDescription() ?? $artworkObj->getExcerpt() ?? '');
$imageFileName = $artworkObj->getImagefilename();
$mediumImage   = artworkImageUrl($imageFileName, 'medium');
$largeImage    = artworkImageUrl($imageFileName, 'large');
 
$dimensions = '';
if ($width && $height) {
    $dimensions = e((string)$width) . ' cm × ' . e((string)$height) . ' cm';
}
 
$isFavorited = isset($_SESSION['favorites']['artworks'])
               && in_array($artworkId, array_map('intval', $_SESSION['favorites']['artworks']), true);
 
$loggedInCustomerId = isLoggedIn() ? (int) ($_SESSION['user']['CustomerID'] ?? 0) : 0;
$alreadyReviewed    = $loggedInCustomerId > 0
                      && $reviewRepo->hasUserReviewedArtwork($artworkId, $loggedInCustomerId);
 
$pageTitle = $title . ' · Kunstwerk';
require_once __DIR__ . '/../includes/header.php';
?>
 
<!-- ===== BOOTSTRAP MODAL for large image ===== -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"><?= e($title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= e($largeImage); ?>" alt="<?= e($title); ?>" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>
 
<!-- ===== ARTWORK DETAIL ===== -->
<article class="artwork-detail-layout">
 
    <!-- Image panel -->
    <div class="artwork-image-panel">
        <img
            src="<?= e($mediumImage); ?>"
            alt="<?= e($title); ?>"
            class="artwork-main-image"
            role="button"
            data-bs-toggle="modal"
            data-bs-target="#imageModal"
            title="Klicken für große Ansicht"
            style="cursor:pointer;"
        >
        <small class="image-hint">Bild anklicken für große Version</small>
    </div>
 
    <!-- Info panel -->
    <div class="artwork-info-panel">
        <p class="eyebrow">Kunstwerk</p>
        <h1><?= e($title); ?></h1>
        <p class="artwork-artist-byline">von
            <a href="<?= e(artistDetailUrl($artistId)); ?>"><?= e($artistName); ?></a>
        </p>
 
        <?php if ($description !== ''): ?>
            <p class="artwork-description"><?= e($description); ?></p>
        <?php endif; ?>
 
        <!-- Favorite button -->
        <?php if (isLoggedIn()): ?>
            <?php if ($isFavorited): ?>
                <a class="btn btn-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/remove-favorite.php') . '?type=artwork&id=' . $artworkId); ?>">
                    ★ Aus Favoriten entfernen
                </a>
            <?php else: ?>
                <a class="btn btn-outline-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/add-favorite.php') . '?type=artwork&id=' . $artworkId); ?>">
                    ☆ Zu Favoriten hinzufügen
                </a>
            <?php endif; ?>
        <?php else: ?>
            <p class="mb-3"><a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a>, um zu favorisieren.</p>
        <?php endif; ?>
 
        <!-- Product Details table -->
        <table class="detail-table table table-bordered">
            <caption class="fw-bold text-start pb-2">Werkdetails</caption>
            <tbody>
                <tr>
                    <th scope="row">Datum</th>
                    <td><?= $year !== '' ? e($year) : '–'; ?></td>
                </tr>
                <?php if ($medium !== ''): ?>
                <tr>
                    <th scope="row">Medium</th>
                    <td><?= e($medium); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($dimensions !== ''): ?>
                <tr>
                    <th scope="row">Abmessungen</th>
                    <td><?= $dimensions; ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row">Galerie</th>
                    <td>
                        <?php if ($galleryObj): ?>
                            <!-- Bootstrap Accordion for gallery info -->
                            <div class="accordion accordion-flush" id="galleryAccordion">
                                <div class="accordion-item border-0 p-0">
                                    <h2 class="accordion-header" id="galleryHeading">
                                        <button
                                            class="accordion-button collapsed p-0 bg-transparent shadow-none fw-normal"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#galleryInfo"
                                            aria-expanded="false"
                                            aria-controls="galleryInfo"
                                        >
                                            <?= e($galleryObj->getGalleryName()); ?>
                                        </button>
                                    </h2>
                                    <div id="galleryInfo" class="accordion-collapse collapse" aria-labelledby="galleryHeading">
                                        <div class="accordion-body ps-0">
                                            <?php if ($galleryObj->getGalleryNativeName()): ?>
                                                <p><strong>Einheimischer Name:</strong> <?= e($galleryObj->getGalleryNativeName()); ?></p>
                                            <?php endif; ?>
                                            <?php if ($galleryObj->getGalleryCountry()): ?>
                                                <p><strong>Land:</strong> <?= e($galleryObj->getGalleryCountry()); ?></p>
                                            <?php endif; ?>
                                            <?php if ($galleryObj->getGalleryWebsite()): ?>
                                                <p><strong>Website:</strong>
                                                    <a href="<?= e($galleryObj->getGalleryWebsite()); ?>" target="_blank" rel="noopener">
                                                        <?= e($galleryObj->getGalleryWebsite()); ?>
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            Keine Galerie hinterlegt
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Genres</th>
                    <td>
                        <?php if (empty($artworkGenres)): ?>
                            –
                        <?php else: ?>
                            <?php foreach ($artworkGenres as $genre): ?>
                                <a class="badge text-bg-secondary me-1"
                                   href="<?= e(base_url('pages/single-genre.php') . '?id=' . $genre->getGenreID()); ?>">
                                    <?= e($genre->getGenreName()); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Themen</th>
                    <td>
                        <?php if (empty($artworkSubjects)): ?>
                            –
                        <?php else: ?>
                            <?php foreach ($artworkSubjects as $subject): ?>
                                <a class="badge text-bg-secondary me-1"
                                   href="<?= e(base_url('pages/single-subject.php') . '?id=' . $subject->getSubjectid()); ?>">
                                    <?= e($subject->getSubjectname()); ?>
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
                            <span class="text-muted">(<?= e((string)$totalReviews); ?> Bewertungen)</span>
                        <?php else: ?>
                            <span class="text-muted">Noch keine Bewertung</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</article>
 
<!-- ===== REVIEWS SECTION ===== -->
<section class="reviews-section mt-4">
    <h2>Bewertungen</h2>
 
    <?php if (isset($_GET['reviewed'])): ?>
        <div class="alert alert-success">Ihre Bewertung wurde gespeichert.</div>
    <?php endif; ?>
 
    <?php if (empty($reviewRows)): ?>
        <p class="text-muted">Für dieses Kunstwerk gibt es noch keine Bewertungen.</p>
    <?php else: ?>
        <?php foreach ($reviewRows as $row): ?>
            <?php
            $stars      = max(0, min(5, (int) ($row['Rating'] ?? 0)));
            $reviewDate = $row['ReviewDate'] ?? '';
            $formatted  = $reviewDate ? date('d.m.Y', strtotime($reviewDate)) : '';
            $username   = (string) ($row['UserName'] ?? 'Unbekannt');
            $city       = (string) ($row['City'] ?? '');
            $country    = (string) ($row['Country'] ?? '');
            $location   = implode(', ', array_filter([$city, $country]));
            $isOwn      = $loggedInCustomerId > 0
                          && isset($row['CustomerId'])
                          && (int)$row['CustomerId'] === $loggedInCustomerId;
            ?>
            <article class="review-card card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="review-stars text-warning">
                                <?= str_repeat('★', $stars) . str_repeat('☆', 5 - $stars); ?>
                            </span>
                            <strong class="ms-2"><?= e($username); ?></strong>
                            <?php if ($isOwn): ?>
                                <span class="badge text-bg-info ms-1">Deine Bewertung</span>
                            <?php endif; ?>
                            <?php if ($location !== ''): ?>
                                <span class="text-muted ms-2 small"><?= e($location); ?></span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= e($formatted); ?></small>
                    </div>
                    <p class="mt-2 mb-1"><?= e((string) ($row['Comment'] ?? '')); ?></p>
 
                    <?php if (isAdmin()): ?>
                        <form method="post" action="<?= e(base_url('pages/delete-review.php')); ?>"
                              onsubmit="return confirm('Bewertung wirklich löschen?');">
                            <input type="hidden" name="review_id"  value="<?= (int)($row['ReviewId'] ?? 0); ?>">
                            <input type="hidden" name="artwork_id" value="<?= $artworkId; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Bewertung löschen</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
 
    <!-- ===== ADD REVIEW FORM ===== -->
    <?php if (isLoggedIn() && !$alreadyReviewed): ?>
        <div class="add-review-box card mt-4">
            <div class="card-body">
                <h3 class="card-title h5">Bewertung hinzufügen</h3>
 
                <?php foreach ($reviewErrors as $err): ?>
                    <div class="alert alert-danger"><?= e($err); ?></div>
                <?php endforeach; ?>
 
                <form method="post" action="<?= e(base_url('pages/single-artwork.php') . '?id=' . $artworkId); ?>">
                    <input type="hidden" name="add_review" value="1">
 
                    <div class="mb-3">
                        <label for="rating" class="form-label fw-bold">Bewertung (1–5)</label>
                        <input
                            type="number"
                            id="rating"
                            name="rating"
                            class="form-control"
                            min="1" max="5"
                            required
                            value="<?= (int) ($_POST['rating'] ?? 3); ?>"
                        >
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
        <p class="mt-3">
            <a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a>, um eine Bewertung zu hinterlassen.
        </p>
    <?php endif; ?>
</section>
 
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
