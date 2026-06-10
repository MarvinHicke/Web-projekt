<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
 
$artistId = (int) ($_GET['id'] ?? 0);
 
if ($artistId <= 0) {
    $pageTitle = 'Künstler nicht gefunden';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-heading">
        <h1>Künstler nicht gefunden</h1>
        <p>Ungültige Künstler-ID.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artists.php')); ?>">Zurück zur Künstlerübersicht</a>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
 
try {
    $db = new dbaccess();
    $db->connect();
 
    // Full raw row to access all DB columns (Details, ArtistLink, etc.)
    $rawStmt = $db->preparedStatement("SELECT * FROM artists WHERE ArtistID = :id");
    $rawStmt->execute(['id' => $artistId]);
    $artistRow = $rawStmt->fetch(PDO::FETCH_ASSOC);
 
    if (!$artistRow) {
        $pageTitle = 'Künstler nicht gefunden';
        require_once __DIR__ . '/../includes/header.php';
        ?>
        <section class="page-heading">
            <h1>Künstler nicht gefunden</h1>
            <p>Kein Künstler mit dieser ID gefunden.</p>
            <a class="button-link" href="<?= e(base_url('pages/browse-artists.php')); ?>">Zurück zur Künstlerübersicht</a>
        </section>
        <?php
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }
 
    $artworkRepo = new artworkRepository($db);
    $artworks    = $artworkRepo->getForArtist($artistId);
 
} catch (Exception $e) {
    $pageTitle = 'Fehler';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-heading">
        <h1>Fehler beim Laden</h1>
        <p>Die Seite konnte nicht geladen werden.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artists.php')); ?>">Zurück zur Künstlerübersicht</a>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
 
// ── computed values ───────────────────────────────────────────────────────────
$firstName   = (string) ($artistRow['FirstName'] ?? '');
$lastName    = (string) ($artistRow['LastName'] ?? '');
$fullName    = trim($firstName . ' ' . $lastName);
$nationality = (string) ($artistRow['Nationality'] ?? '');
$birthYear   = (string) ($artistRow['BirthYear'] ?? '');
$deathYear   = (string) ($artistRow['DeathYear'] ?? '');       // may not exist
$details     = (string) ($artistRow['Details'] ?? '');          // biography
$artistLink  = (string) ($artistRow['ArtistLink'] ?? '');       // Wikipedia etc.
 
// Build date string: "1853 – 1890" or just "1853" if no death year
$dateString = $birthYear;
if ($deathYear !== '') {
    $dateString .= ' – ' . $deathYear;
}
 
$artistPhoto = artistImageUrl($artistId, 'medium');
 
$isFavorited = isset($_SESSION['favorites']['artists'])
               && in_array($artistId, array_map('intval', $_SESSION['favorites']['artists']), true);
 
$pageTitle = $fullName . ' · Künstler';
require_once __DIR__ . '/../includes/header.php';
?>
 
<!-- ===== ARTIST HERO ===== -->
<section class="artist-detail-layout">
 
    <!-- Photo panel -->
    <div class="artist-image-panel">
        <img
            src="<?= e($artistPhoto); ?>"
            alt="<?= e($fullName); ?>"
            class="artist-photo"
        >
    </div>
 
    <!-- Info panel -->
    <div class="artist-info-panel">
        <h1><?= e($fullName); ?></h1>
 
        <?php if ($details !== ''): ?>
            <p class="artist-bio"><?= e($details); ?></p>
        <?php endif; ?>
 
        <!-- Favorite button -->
        <?php if (isLoggedIn()): ?>
            <?php if ($isFavorited): ?>
                <a class="btn btn-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/remove-favorite.php') . '?type=artist&id=' . $artistId); ?>">
                    ★ Aus Favoriten entfernen
                </a>
            <?php else: ?>
                <a class="btn btn-outline-warning btn-sm mb-3"
                   href="<?= e(base_url('pages/add-favorite.php') . '?type=artist&id=' . $artistId); ?>">
                    ☆ Zu Favoriten hinzufügen
                </a>
            <?php endif; ?>
        <?php else: ?>
            <p class="mb-3">
                <a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a>, um zu favorisieren.
            </p>
        <?php endif; ?>
 
        <!-- Artist Details table -->
        <table class="detail-table table table-bordered">
            <caption class="fw-bold text-start pb-2">Künstlerdetails</caption>
            <tbody>
                <?php if ($dateString !== ''): ?>
                <tr>
                    <th scope="row">Datum</th>
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
<section class="artist-artworks-section mt-5">
    <h2>Kunst von <?= e($fullName); ?></h2>
 
    <?php if (empty($artworks)): ?>
        <p class="text-muted">Keine Kunstwerke für diesen Künstler gefunden.</p>
    <?php else: ?>
        <div class="artwork-card-grid">
            <?php foreach ($artworks as $artwork): ?>
                <?php
                $awId       = $artwork->getArtworkid();
                $awTitle    = $artwork->getTitle();
                $awYear     = (string) ($artwork->getYearofwork() ?? '');
                $awFileName = $artwork->getImagefilename();
                ?>
                <article class="artwork-card-link-wrapper">
                    <div class="card h-100 text-center">
                        <a href="<?= e(artworkDetailUrl($awId)); ?>">
                            <img
                                src="<?= e(artworkImageUrl($awFileName, 'square-small')); ?>"
                                alt="<?= e($awTitle); ?>"
                                class="card-img-top artwork-card-image"
                            >
                        </a>
                        <div class="card-body">
                            <p class="card-text small">
                                <?= e($awTitle); ?>
                                <?= $awYear !== '' ? ', ' . e($awYear) : ''; ?>
                            </p>
                            <a class="btn btn-sm btn-primary" href="<?= e(artworkDetailUrl($awId)); ?>">
                                Ansehen
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
 
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
