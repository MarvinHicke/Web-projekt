<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$artistId = (int) ($_GET['id'] ?? 0);

if ($artistId <= 0) {
    $pageTitle = 'Künstler nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    $db = new dbaccess();
    $db->connect();

    $artistObj = (new artistRepository($db))->getById($artistId);

    // Raw SELECT * to capture all DB columns including Details, ArtistLink, BirthYear, DeathYear
    $stmt = $db->preparedStatement("SELECT * FROM artists WHERE ArtistID = :id");
    $stmt->execute(['id' => $artistId]);
    $artistRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artistRow) {
        $pageTitle = 'Künstler nicht gefunden';
        require_once __DIR__ . '/../includes/header.php';
        echo '<section class="page-heading"><h1>Künstler nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    $artists = (new artistRepository($db))->findAll();

} catch (Exception $e) {
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// ── display values ─────────────────────────────────────────────────────────
$firstName   = (string) ($artistRow['FirstName']   ?? '');
$lastName    = (string) ($artistRow['LastName']     ?? '');
$fullName    = trim($firstName . ' ' . $lastName);
$nationality = (string) ($artistRow['Nationality'] ?? $artistRow['nationality'] ?? '');
// Details and ArtistLink — try multiple casing variants (DB column names vary)
$details     = cleanHtml(
        $artistRow['Details']     ??
        $artistRow['details']     ??
        $artistRow['Description'] ?? ''
);
$artistLink  = (string) (
        $artistRow['ArtistLink']  ??
        $artistRow['artistlink']  ??
        $artistRow['Artistlink']  ??
        $artistRow['Link']        ?? ''
);

// Date — try BirthYear with multiple casing variants (DB column names vary)
$birthYear   = (string) (
        $artistRow['BirthYear']   ??
        $artistRow['birthyear']   ??
        $artistRow['Birthyear']   ??
        $artistRow['birth_year']  ?? ''
);
$deathYear   = (string) (
        $artistRow['DeathYear']   ??
        $artistRow['deathyear']   ??
        $artistRow['Deathyear']   ??
        $artistRow['death_year']  ?? ''
);
if ($birthYear !== '' && $deathYear !== '') {
    $dateString = $birthYear . ' – ' . $deathYear;
} elseif ($birthYear !== '') {
    $dateString = $birthYear;
} else {
    $dateString = '';
}

$width         = $artistObj->getWidth();
$height        = $artistObj->getHeight();
$imageFileName = $artistObj->getImagefilename();
$mediumImage   = artistImageUrl($imageFileName, 'medium');
$largeImage    = artistImageUrl($imageFileName, 'large');

$dimensions = ($width && $height)
        ? e((string)$width) . ' cm × ' . e((string)$height) . ' cm'
        : '';

$isFavorited = isset($_SESSION['favorites']['artists'])
        && in_array($artistId, array_map('intval', $_SESSION['favorites']['artists']), true);

$pageTitle = $fullName . ' · Künstler';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="imageModalLabel"><?= e($fullName); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img src="<?= e($largeImage); ?>" alt="<?= e($fullName); ?>" class="img-fluid">
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>

<section class="artist-detail-layout">

    <div class="artist-image-panel">
        <img src="<?= e($mediumImage); ?>"
             alt="<?= e($fullName); ?>"
             class="artwork-main-image img-fluid"
             role="button"
             data-bs-toggle="modal"
             data-bs-target="#imageModal"
             title="Klicken für große Ansicht"
             style="cursor:zoom-in;"
        >
        <small class="d-block text-muted mt-1">Klicken für große Version</small>    </div>

    <div class="artist-info-panel">
        <h1><?= e($fullName); ?></h1>

        <?php if ($details !== ''): ?>
            <p class="artist-bio"><?= e($details); ?></p>
        <?php endif; ?>

        <!-- Favorite button -->
        <?php if ($isFavorited): ?>
            <a class="btn btn-primary btn-sm mb-3"
               href="<?= e(base_url('pages/remove-favorite.php') . '?type=artist&id=' . $artistId . '&redirect=single-artist.php'); ?>">
                ★ Aus Favoriten entfernen
            </a>
        <?php else: ?>
            <a class="btn btn-outline-primary btn-sm mb-3"
               href="<?= e(base_url('pages/add-favorite.php') . '?type=artist&id=' . $artistId . '&redirect=single-artist.php'); ?>">
                ☆ Zu Favoriten hinzufügen
            </a>
        <?php endif; ?>

        <!-- Details table -->
        <table class="table table-bordered">
            <caption class="fw-bold text-start pb-2 caption-top">Künstlerdetails</caption>
            <tbody>
            <?php if ($dateString !== ''): ?>
                <tr>
                    <th scope="row" style="width:35%">Datum</th>
                    <td><?= e($dateString); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($nationality !== ''): ?>
                <tr>
                    <th scope="row">Nationalität</th>
                    <td><?= e($nationality); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($artistLink !== ''): ?>
                <tr>
                    <th scope="row">Weitere Infos</th>
                    <td>
                        <a href="<?= e($artistLink); ?>" target="_blank" rel="noopener">
                            <?= e($artistLink); ?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ===== ARTWORKS GRID ===== -->
<section class="mt-5">
    <h2>Kunst von <?= e($fullName); ?></h2>

    <?php if (empty($artworks)): ?>
        <p class="text-muted">Keine Kunstwerke für diesen Künstler gefunden.</p>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($artworks as $artwork): ?>
                <?php
                $awId       = $artwork->getArtworkid();
                $awTitle    = $artwork->getTitle();
                $awYear     = (string) ($artwork->getYearofwork() ?? '');
                $awFileName = $artwork->getImagefilename();
                ?>
                <div class="col">
                    <div class="card h-100 text-center">
                        <a href="<?= e(artworkDetailUrl($awId)); ?>">
                            <img src="<?= e(artworkImageUrl($awFileName, 'square-small')); ?>"
                                 alt="<?= e($awTitle); ?>"
                                 class="card-img-top"
                                 style="height:160px; object-fit:cover;">
                        </a>
                        <div class="card-body p-2">
                            <p class="card-text small mb-2">
                                <?= e($awTitle); ?>
                                <?= $awYear !== '' ? ', ' . e($awYear) : ''; ?>
                            </p>
                            <a class="btn btn-sm btn-primary" href="<?= e(artworkDetailUrl($awId)); ?>">
                                Ansehen
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>