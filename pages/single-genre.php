<?php
// Load application initialization, shared helpers, and required repositories.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Read and validate the genre ID from the query string.
$genreId = (int) ($_GET['id'] ?? 0);

// Stop early if the provided genre ID is invalid.
if ($genreId <= 0) {
    $pageTitle = 'Genre nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Open the database connection.
    $db = new dbaccess();
    $db->connect();

    // Load the genre model object.
    $genreRepository = new genreRepository($db);
    $genreObj = $genreRepository->getById($genreId);

    // Load the raw genre row to access all available database columns.
    // This is useful when optional column names vary between dataset versions.
    $stmt = $db->preparedStatement("SELECT * FROM genres WHERE GenreID = :id");
    $stmt->execute(['id' => $genreId]);
    $genreRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Show a user-friendly message if no genre exists for the given ID.
    if (!$genreRow) {
        $pageTitle = 'Genre nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Genre nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Load all artworks assigned to the current genre.
    $artworks = (new artworkRepository($db))->getForGenre($genreId);

} catch (Throwable $e) {
    // Show a fallback error page if loading the genre or related artworks fails.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Prepare display values for the genre detail page.
$genreName = (string) ($genreRow['GenreName'] ?? '');
$genreEra = (string) ($genreRow['Era'] ?? '');
$genreDescription = cleanHtml((string) ($genreRow['Description'] ?? ''));

// Read the external genre link from possible database column variants.
$genreLink = (string) (
        $genreRow['GenreLink'] ??
        $genreRow['Genrelink'] ??
        $genreRow['genrelink'] ??
        $genreRow['Link']      ??
        ''
);

// Build the main genre image URL.
$genrePhoto = genreImageUrl($genreId);

// Set the final page title and render the shared header.
$pageTitle = $genreName . ' · Genre';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Main genre detail layout with image and metadata. -->
    <section class="genre-detail-layout">

        <!-- Genre image panel. -->
        <div class="genre-image-panel">
            <img src="<?= e($genrePhoto); ?>" alt="<?= e($genreName); ?>">
        </div>

        <!-- Genre information panel with description and metadata. -->
        <div class="genre-info-panel">
            <h1><?= e($genreName); ?></h1>

            <?php if ($genreDescription !== ''): ?>
                <p><strong>Beschreibung:</strong> <?= e($genreDescription); ?></p>
            <?php endif; ?>

            <!-- Genre metadata table. -->
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

    <!-- Related artworks assigned to the current genre. -->
    <section class="mt-5">
        <h2>Kunstwerke des Genre '<?= e($genreName); ?>'</h2>

        <?php if (empty($artworks)): ?>
            <!-- Empty-state message shown when the genre has no assigned artworks. -->
            <p class="text-muted">Keine Kunstwerke für dieses Genre gefunden.</p>
        <?php else: ?>
            <!-- Responsive grid of related artwork cards. -->
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <?php foreach ($artworks as $artwork): ?>
                    <?php
                    // Prepare display values for the current artwork card.
                    $awId       = $artwork->getArtworkid();
                    $awTitle    = $artwork->getTitle();
                    $awYear     = (string) ($artwork->getYearofwork() ?? '');
                    $awFileName = $artwork->getImagefilename();
                    ?>

                    <div class="col">
                        <div class="card h-100 text-center">
                            <a href="<?= e(artworkDetailUrl($awId)); ?>">
                                <img
                                        src="<?= e(artworkImageUrl($awFileName, 'square-medium')); ?>"
                                        alt="<?= e($awTitle); ?>"
                                        class="card-img-top"
                                        style="height:160px; object-fit:cover;"
                                >
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

<?php
// Render the shared footer.
require_once __DIR__ . '/../includes/footer.php';
?>