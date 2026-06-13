<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

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

    $genreRepository = new genreRepository($db);
    $genreObj = $genreRepository->getById($genreId);

    // Read the raw row because link column names vary between dataset versions.
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
$genreName = (string) ($genreRow['GenreName'] ?? '');
$genreEra = (string) ($genreRow['Era'] ?? '');
$genreDescription = cleanHtml((string) ($genreRow['Description'] ?? ''));
// Details and ArtistLink — try multiple casing variants (DB column names vary)
$genreLink  = (string) (
        $genreRow['GenreLink']  ??
        $genreRow['Genrelink']  ??
        $genreRow['genrelink']  ??
        $genreRow['Link']        ?? ''
);

$genrePhoto = genreImageUrl($genreId);

$pageTitle = $genreName . ' · Genre';
require_once __DIR__ . '/../includes/header.php';
?>

    <section class="genre-detail-layout">

        <div class="genre-image-panel">
            <img src="<?= e($genrePhoto); ?>" alt="<?= e($genreName); ?>">

        </div>

        <div class="genre-info-panel">
            <h1><?= e($genreName); ?></h1>

            <?php if ($genreDescription !== ''): ?>
                <p><?= e($genreDescription); ?></p>
            <?php endif; ?>

            <table class="table table-bordered">
                <tbody>
                <?php if ($genreEra !== ''): ?>
                    <tr>
                        <th scope="row">Epoche</th>
                        <td><?= e($genreEra); ?></td>
                    </tr>
                <?php endif; ?>
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
        <h2>Kunstwerke dieses Genres</h2>

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
