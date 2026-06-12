<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

$subjectId = (int) ($_GET['id'] ?? 0);

if ($subjectId <= 0) {
    $pageTitle = 'Subject nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
        . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    $db = new dbaccess();
    $db->connect();

    // Raw SELECT * to capture all DB columns including Details, ArtistLink, BirthYear, DeathYear
    $stmt = $db->preparedStatement("SELECT * FROM subjects WHERE SubjectID = :id");
    $stmt->execute(['id' => $subjectId]);
    $genreRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$genreRow) {
        $pageTitle = 'Subject nicht gefunden';
        require_once __DIR__ . '/../includes/header.php';
        echo '<section class="page-heading"><h1>Subject nicht gefunden</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    $artworks = (new artworkRepository($db))->getForSubject($subjectId);

} catch (Exception $e) {
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-heading"><h1>Ladefehler</h1>'
        . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück</a></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// ── display values ─────────────────────────────────────────────────────────
$subjectName   = (string) ($subjectRow['SubjectName']   ?? '');
// Details and ArtistLink — try multiple casing variants (DB column names vary)
$subjectLink  = (string) (
    $subjectRow['SubjectLink']  ??
    $subjectRow['subjectlink']  ??
    $subjectRow['Subjectlink']  ??
    $subjectRow['Link']        ?? ''
);

$subjectPhoto = subjectImageUrl($subjectId);

$isFavorited = isset($_SESSION['favorites']['subject'])
    && in_array($subjectId, array_map('intval', $_SESSION['favorites']['subject']), true);

$pageTitle = $subjectName . ' · Subject';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="subject-detail-layout">

    <div class="subject-image-panel">
        <img src="<?= e($subjectPhoto); ?>" alt="<?= e($subjectName); ?>" class="subject-photo img-fluid">
    </div>

    <div class="subject-info-panel">
        <h1><?= e($subjectName); ?></h1>

        <!-- Favorite button -->
        <?php if (isLoggedIn()): ?>
            <?php if ($isFavorited): ?>
                <a class="btn btn-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/remove-favorite.php') . '?type=subject&id=' . $subjectId); ?>">
                    ★ Aus Favoriten entfernen
                </a>
            <?php else: ?>
                <a class="btn btn-outline-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/add-favorite.php') . '?type=subject&id=' . $subjectId); ?>">
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
            <caption class="fw-bold text-start pb-2 caption-top">Subjectdetails</caption>
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

<!-- ===== ARTWORKS GRID ===== -->
<section class="mt-5">
    <h2>Subject <?= e($subjectName); ?></h2>

    <?php if (empty($artworks)): ?>
        <p class="text-muted">Keine Kunstwerke für dieses Subject gefunden.</p>
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