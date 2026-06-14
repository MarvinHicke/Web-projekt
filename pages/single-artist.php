<?php
// Load application initialization, shared helpers, and required repositories.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Read and validate the artist ID from the query string.
$artistId = (int) ($_GET['id'] ?? 0);

// Stop early if the provided artist ID is invalid.
if ($artistId <= 0) {
    $pageTitle = 'Künstler nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Open the database connection.
    $db = new dbaccess();
    $db->connect();

    // Load the artist model object for image and dimension helper methods.
    $artistObj = (new artistRepository($db))->getById($artistId);

    // Load the raw artist row to access all available database columns.
    // This is useful when the model does not expose every optional field.
    $stmt = $db->preparedStatement("SELECT * FROM artists WHERE ArtistID = :id");
    $stmt->execute(['id' => $artistId]);
    $artistRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Show a user-friendly message if no artist exists for the given ID.
    if (!$artistRow) {
        $pageTitle = 'Künstler nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Künstler nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Load all artworks assigned to the current artist.
    $artworks = (new artworkRepository($db))->getForArtist($artistId);

} catch (Throwable $e) {
    // Show a fallback error page if loading the artist or related artworks fails.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Prepare display values for the artist detail page.
$firstName   = (string) ($artistRow['FirstName'] ?? '');
$lastName    = (string) ($artistRow['LastName'] ?? '');
$fullName    = trim($firstName . ' ' . $lastName);
$nationality = (string) ($artistRow['Nationality'] ?? $artistRow['nationality'] ?? '');

// Read biography/details from possible database column variants.
$details = cleanHtml(
        $artistRow['Details']     ??
        $artistRow['details']     ??
        $artistRow['Description'] ??
        ''
);

// Read the external artist link from possible database column variants.
$artistLink = (string) (
        $artistRow['ArtistLink'] ??
        $artistRow['artistlink'] ??
        $artistRow['Artistlink'] ??
        $artistRow['Link']       ??
        ''
);

// Read birth and death years from possible database column variants.
$birthYear = (string) (
        $artistRow['BirthYear']  ??
        $artistRow['birthyear']  ??
        $artistRow['Birthyear']  ??
        $artistRow['birth_year'] ??
        ''
);

$deathYear = (string) (
        $artistRow['DeathYear']  ??
        $artistRow['deathyear']  ??
        $artistRow['Deathyear']  ??
        $artistRow['death_year'] ??
        ''
);

// Build a readable life date string.
if ($birthYear !== '' && $deathYear !== '') {
    $dateString = $birthYear . ' – ' . $deathYear;
} elseif ($birthYear !== '') {
    $dateString = $birthYear;
} else {
    $dateString = '';
}

// Prepare image and dimension data for the artist image panel.
$imageFileName = $artistObj->getImagefilename();
$mediumImage   = artistImageUrl($imageFileName, 'medium');

// Check whether the current artist is already stored as a session favorite.
$isFavorited = isset($_SESSION['favorites']['artists'])
        && in_array($artistId, array_map('intval', $_SESSION['favorites']['artists']), true);

// Set the final page title and render the shared header.
$pageTitle = $fullName . ' · Künstler';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Main artist detail layout with image and metadata. -->
    <section class="artist-detail-layout">

        <!-- Artist image panel with clickable modal preview. -->
        <div class="artist-image-panel">
            <img
                    src="<?= e($mediumImage); ?>"
                    alt="<?= e($fullName); ?>"
                    class="artwork-main-image img-fluid"
            >
            <small class="d-block text-muted mt-1"></small>
        </div>

        <!-- Artist information panel with biography, favorites, and metadata. -->
        <div class="artist-info-panel">
            <h1><?= e($fullName); ?></h1>

            <?php if ($details !== ''): ?>
                <p class="artist-bio"><?= e($details); ?></p>
            <?php endif; ?>

            <!-- Session-based favorite button for artists. -->
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

            <!-- Artist metadata table. -->
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

    <!-- Related artworks created by the current artist. -->
    <section class="mt-5">
        <h2>Kunstwerke von <?= e($fullName); ?></h2>

        <?php if (empty($artworks)): ?>
            <!-- Empty-state message shown when the artist has no assigned artworks. -->
            <p class="text-muted">Keine Kunstwerke für diesen Künstler gefunden.</p>
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