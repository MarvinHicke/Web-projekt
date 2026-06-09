<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/mock-data.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';

$artworkId = (int) ($_GET['id'] ?? 0);

$reviewErrors = [];
$reviewSuccessMessage = '';
$newReviewRating = 0;
$newReviewComment = '';
$reviewCommentMaxLength = 2000;

$currentCustomerId = isLoggedIn() ? (int) ($_SESSION['user']['CustomerID'] ?? 0) : 0;
$hasCurrentUserReviewedArtwork = false;

$reviewStatus = (string) ($_GET['review'] ?? '');

if ($reviewStatus === 'added') {
    $reviewSuccessMessage = 'Ihre Bewertung wurde gespeichert.';
} elseif ($reviewStatus === 'deleted') {
    $reviewSuccessMessage = 'Die Bewertung wurde gelöscht.';
}

$selectedArtwork = null;

if (!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}

if (!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}

$favoriteArtworkIds = array_map('intval', $_SESSION["favorites"]["artworks"]);
$isFavoriteArtwork = $artworkId > 0 && in_array($artworkId, $favoriteArtworkIds, true);

foreach ($artworks as $artwork) {
    $currentId = (int) ($artwork['id'] ?? $artwork['ArtWorkID'] ?? 0);
    if ($currentId === $artworkId) {
        $selectedArtwork = $artwork;
        break;
    }
}

$fromDb = false;

if ($selectedArtwork === null) {
    try {
        $db = new dbaccess();
        $db->connect();
        $artworkRepository = new artworkRepository($db);
        $artworkObj = $artworkRepository->getById($artworkId);

        if ($artworkObj !== null) {
            $fromDb = true;
            $artistRepository = new artistRepository($db);
            $artistObj = $artistRepository->getById($artworkObj->getArtistid());

            $title = $artworkObj->getTitle();
            $artistId = $artworkObj->getArtistid();
            $artistName = $artistObj
                ? trim($artistObj->getFirstName() . ' ' . $artistObj->getLastName())
                : 'Unbekannter Künstler';
            $year = $artworkObj->getYearofwork() ?? 'Unbekannt';
            $imageFileName = $artworkObj->getImagefilename();
            $image = $imageFileName !== '' ? artworkImageUrl($imageFileName, 'medium') : '';
            $largeImage = $imageFileName !== '' ? artworkImageUrl($imageFileName, 'large') : '';
            $genre = 'Nicht hinterlegt';
            $subjects = [];
            $gallery = 'Keine Galerie hinterlegt';
            $reviewRepository = new reviewRepository($db);

            if ($currentCustomerId > 0) {
                $hasCurrentUserReviewedArtwork = $reviewRepository->hasUserReviewedArtwork($artworkId, $currentCustomerId);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['formType'] ?? '') === 'deleteReview')
            {
                $reviewIdToDelete = (int) ($_POST['reviewId'] ?? 0);

                if (!isAdmin()) {
                    $reviewErrors[] = 'Sie dürfen keine Bewertungen löschen.';
                }

                if ($reviewIdToDelete <= 0) {
                    $reviewErrors[] = 'Die Bewertung konnte nicht gefunden werden.';
                }

                if (empty($reviewErrors)) {
                    $reviewToDelete = $reviewRepository->getById($reviewIdToDelete);

                    if (!$reviewToDelete || (int) $reviewToDelete->getArtworkId() !== $artworkId) {
                        $reviewErrors[] = 'Die Bewertung gehört nicht zu diesem Kunstwerk.';
                    }
                }

                if (empty($reviewErrors)) {
                    $deleted = $reviewRepository->deleteReview($reviewIdToDelete);

                    if ($deleted) {
                        header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . urlencode((string) $artworkId) . '&review=deleted#reviews');
                        exit;
                    }

                    $reviewErrors[] = 'Die Bewertung konnte nicht gelöscht werden.';
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['formType'] ?? '') === 'review')
            {
                $newReviewRating = (int) ($_POST['rating'] ?? 0);
                $newReviewComment = trim(strip_tags((string) ($_POST['comment'] ?? '')));

                if ($currentCustomerId <= 0) {
                    $reviewErrors[] = 'Sie müssen angemeldet sein, um eine Bewertung zu schreiben.';
                }

                if ($hasCurrentUserReviewedArtwork) {
                    $reviewErrors[] = 'Sie haben dieses Kunstwerk bereits bewertet.';
                }

                if ($newReviewRating < 1 || $newReviewRating > 5) {
                    $reviewErrors[] = 'Die Bewertung muss zwischen 1 und 5 liegen.';
                }

                if ($newReviewComment === '') {
                    $reviewErrors[] = 'Bitte geben Sie einen Bewertungstext ein.';
                }

                if (strlen($newReviewComment) > $reviewCommentMaxLength) {
                    $reviewErrors[] = 'Der Bewertungstext darf maximal ' . $reviewCommentMaxLength . ' Zeichen lang sein.';
                }

                if (empty($reviewErrors)) {
                    $reviewCreated = $reviewRepository->addReview(
                            $artworkId,
                            $currentCustomerId,
                            $newReviewRating,
                            $newReviewComment
                    );

                    if ($reviewCreated) {
                        header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . urlencode((string) $artworkId) . '&review=added#reviews');
                        exit;
                    }

                    $reviewErrors[] = 'Die Bewertung konnte nicht gespeichert werden.';
                }
            }

            $artworkReviews = $reviewRepository->getForArtwork($artworkId);

            $ratingSummary = $reviewRepository->getAverageRatingArtwork($artworkId);
            $averageRating = null;

            if ($ratingSummary && (int) ($ratingSummary['TotalReviews'] ?? 0) > 0)
            {
                $averageRating = (float) ($ratingSummary['AvgRating'] ?? 0);
            }
        }
    } catch (Exception $e) {
        $selectedArtwork = null;
    }
}

if (!$fromDb && $selectedArtwork === null) {
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

if (!$fromDb) {
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

            <?php if ($isFavoriteArtwork): ?>
                <p>Dieses Kunstwerk ist bereits in Ihren Favoriten.</p>

                <?php
                $buttonText = 'Favoriten anzeigen';
                $buttonHref = base_url('pages/favorites.php');
                $buttonVariant = 'primary';
                include __DIR__ . '/../components/button.php';
                ?>

            <?php else: ?>
                <p>Fügen Sie dieses Kunstwerk Ihrer Favoritenliste hinzu.</p>

                <?php
                $buttonText = 'Zu Favoriten hinzufügen';
                $buttonHref = base_url('pages/add-favorite.php')
                        . '?type=artwork&id=' . urlencode((string) $artworkId)
                        . '&redirect=single-artwork.php';
                $buttonVariant = 'outline-primary';
                include __DIR__ . '/../components/button.php';
                ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="reviews-section">
    <h2>Bewertungen</h2>

    <?php if (!empty($reviewErrors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($reviewErrors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($reviewSuccessMessage !== ''): ?>
        <div class="alert alert-success">
            <?= e($reviewSuccessMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>
        <p>Sie müssen angemeldet sein, um eine Bewertung zu schreiben.</p>

        <?php
        $buttonText = 'Zum Login';
        $buttonHref = base_url('pages/login.php');
        $buttonVariant = 'primary';
        include __DIR__ . '/../components/button.php';
        ?>

    <?php elseif ($hasCurrentUserReviewedArtwork): ?>
        <p>Sie haben dieses Kunstwerk bereits bewertet.</p>

    <?php else: ?>
        <h3>Bewertung schreiben</h3>

        <form method="post">
            <input type="hidden" name="formType" value="review">

            <div class="mb-3">
                <label class="form-label" for="rating">Bewertung</label>
                <input
                        class="form-control"
                        type="number"
                        id="rating"
                        name="rating"
                        min="1"
                        max="5"
                        value="<?= e((string) ($newReviewRating > 0 ? $newReviewRating : 5)) ?>"
                        required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="comment">Kommentar</label>
                <textarea
                        class="form-control"
                        id="comment"
                        name="comment"
                        maxlength="<?= $reviewCommentMaxLength ?>"
                        required
                ><?= e($newReviewComment) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Bewertung speichern
            </button>
        </form>
    <?php endif; ?>

    <?php if (empty($artworkReviews)): ?>
        <p>Für dieses Kunstwerk gibt es noch keine Bewertungen.</p>
    <?php endif; ?>

    <?php foreach ($artworkReviews as $review): ?>
        <?php
        if ($review instanceof review) {
            $reviewUser = $review->getCustomerName();
            $reviewRating = (int) $review->getRating();
            $reviewComment = $review->getComment();
            $reviewComment = trim(strip_tags($reviewComment));
            $reviewDate = $review->getReviewDateFormatted();
        } else {
            $reviewUser = (string) ($review['user'] ?? 'Unbekannter Nutzer');
            $reviewRating = (int) ($review['rating'] ?? 0);
            $reviewComment = (string) ($review['text'] ?? $review['comment'] ?? '');
            $reviewDate = (string) ($review['date'] ?? $review['ReviewDate'] ?? '');
        }
        ?>

        <article class="review-card">
            <h3><?= e($reviewUser); ?></h3>

            <?php
            $rating = $reviewRating;
            include __DIR__ . '/../components/star-rating.php';
            ?>

            <p><?= e($reviewComment); ?></p>
            <small><?= e($reviewDate); ?></small>

            <?php if (isAdmin() && $review instanceof review): ?>
                <form
                        method="post"
                        onsubmit="return confirm('Möchten Sie diese Bewertung wirklich löschen?');"
                >
                    <input type="hidden" name="formType" value="deleteReview">
                    <input type="hidden" name="reviewId" value="<?= e((string) $review->getReviewId()) ?>">

                    <button type="submit" class="btn btn-danger">
                        Bewertung löschen
                    </button>
                </form>
            <?php endif; ?>

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