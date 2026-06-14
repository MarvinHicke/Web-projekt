<?php
// Load application initialization, shared helpers, and required repositories.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Read and validate the subject ID from the query string.
$subjectId = (int) ($_GET['id'] ?? 0);

// Stop early if the provided subject ID is invalid.
if ($subjectId <= 0) {
    $pageTitle = 'Thema nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Open the database connection.
    $db = new dbaccess();
    $db->connect();

    // Load the raw subject row to access all available database columns.
    // This is useful when optional column names vary between dataset versions.
    $stmt = $db->preparedStatement("SELECT * FROM subjects WHERE SubjectID = :id");
    $stmt->execute(['id' => $subjectId]);
    $subjectRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Show a user-friendly message if no subject exists for the given ID.
    if (!$subjectRow) {
        $pageTitle = 'Thema nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Thema nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Load all artworks assigned to the current subject.
    $artworks = (new artworkRepository($db))->getForSubject($subjectId);

} catch (Throwable $e) {
    // Show a fallback error page if loading the subject or related artworks fails.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Prepare display values for the subject detail page.
$subjectName = (string) ($subjectRow['SubjectName'] ?? '');

// Read the external subject link from possible database column variants.
$subjectLink = (string) (
        $subjectRow['SubjectLink'] ??
        $subjectRow['subjectlink'] ??
        $subjectRow['Subjectlink'] ??
        $subjectRow['Link']        ??
        ''
);

// Build the main subject image URL.
$subjectPhoto = subjectImageUrl($subjectId);

// Set the final page title and render the shared header.
$pageTitle = $subjectName . ' · Thema';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Main subject detail layout with image and metadata. -->
    <section class="subject-detail-layout">

        <!-- Subject image panel. -->
        <div class="subject-image-panel">
            <img src="<?= e($subjectPhoto); ?>" alt="<?= e($subjectName); ?>">
        </div>

        <!-- Subject information panel with external reference link. -->
        <div class="subject-info-panel">
            <h1><?= e($subjectName); ?></h1>

            <!-- Subject metadata table. -->
            <table class="table table-bordered">
                <tbody>
                <?php if ($subjectLink !== ''): ?>
                    <tr>
                        <th scope="row">Weitere Infos</th>
                        <td>
                            <a href="<?= e($subjectLink); ?>" target="_blank" rel="noopener">
                                <?= e($subjectLink); ?>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Related artworks assigned to the current subject. -->
    <section class="mt-5">
        <h2>Kunstwerke zum Thema '<?= e($subjectName); ?>'</h2>

        <?php if (empty($artworks)): ?>
            <!-- Empty-state message shown when the subject has no assigned artworks. -->
            <p class="text-muted">Keine Kunstwerke für dieses Thema gefunden.</p>
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
                                        src="<?= e(artworkImageUrl($awFileName, 'square-small')); ?>"
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