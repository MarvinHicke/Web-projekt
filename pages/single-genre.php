<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$genreId = (int) ($_GET['id'] ?? 0);

if ($genreId <= 0) {
    $pageTitle = 'Genre nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
        . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    $db = new dbaccess();
    $db->connect();

    // Raw SELECT * to capture all DB columns including Details, ArtistLink, BirthYear, DeathYear
    $stmt = $db->preparedStatement("SELECT * FROM genres WHERE GenreID = :id");
    $stmt->execute(['id' => $genreId]);
    $genreRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$genreRow) {
        $pageTitle = 'Genre nicht gefunden';
        require_once __DIR__ . '/../includes/header.php';
        echo '<section class="page-heading"><h1>Genre nicht gefunden</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    $artworks = (new artworkRepository($db))->getForGenre($genreId);

} catch (Exception $e) {
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ladefehler</h1>'
        . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// ── display values ─────────────────────────────────────────────────────────
$genreName   = (string) ($genreRow['GenreName']   ?? '');
$era    = (string) ($genreRow['Era']     ?? '');
$description = (string) ($genreRow['Description']);
// Details and ArtistLink — try multiple casing variants (DB column names vary)
$details     = cleanHtml(
    $genreRow['Details']     ??
    $genreRow['details']     ??
    $genreRow['Description'] ?? ''
);
$genreLink  = (string) (
    $genreRow['GenreLink']  ??
    $genreRow['genrelink']  ??
    $genreRow['Genrelink']  ??
    $genreRow['Link']        ?? ''
);

$genrePhoto = genreImageUrl($genreId);

$isFavorited = isset($_SESSION['favorites']['genre'])
    && in_array($genreId, array_map('intval', $_SESSION['favorites']['genre']), true);

$pageTitle = $genreName . ' · Genre';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="genre-detail-layout">

    <div class="genre-image-panel">
        <img src="<?= e($genrePhoto); ?>" alt="<?= e($genreName); ?>" class="genre-photo img-fluid">
    </div>

    <div class="genre-info-panel">
        <h1><?= e($genreName); ?></h1>

        <?php if ($details !== ''): ?>
            <p class="genre-bio"><?= e($details); ?></p>
        <?php endif; ?>

        <!-- Favorite button -->
        <?php if (isLoggedIn()): ?>
            <?php if ($isFavorited): ?>
                <a class="btn btn-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/remove-favorite.php') . '?type=genre&id=' . $genreId); ?>">
                    ★ Aus Favoriten entfernen
                </a>
            <?php else: ?>
                <a class="btn btn-outline-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/add-favorite.php') . '?type=genre&id=' . $genreId); ?>">
                    ☆ Zu Favoriten hinzufügen
                </a>
            <?php endif; ?>
        <?php else: ?>
            <p class="mb-3 small">
                <a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a>, um zu favorisieren.
            </p>
        <?php endif; ?>

        <!-- Details table -->
        <table class="table table-bordered">
            <caption class="fw-bold text-start pb-2 caption-top">Genredetails</caption>
            <tbody>
            <?php if ($genreLink !== ''): ?>
                <tr>
                    <th scope="row">Weitere Infos</th>
                    <td>
                        <a href="<?= e($genreLink); ?>" target="_blank" rel="noopener">
                            <?= e($genreLink); ?>
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
    <h2>Genre <?= e($genreName); ?></h2>

    <?php if (empty($artworks)): ?>
        <p class="text-muted">Keine Kunstwerke für dieses Genre gefunden.</p>
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