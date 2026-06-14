<?php
// Enable detailed error output during development.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set the page title before loading the shared header.
$pageTitle = 'Künstler durchsuchen';

// Load application initialization, shared helpers, and the artist repository.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

// Read and validate sorting parameters from the query string.
$sort = safeParam((string) ($_GET['sort'] ?? 'firstName'), ['firstName', 'lastName'], 'firstName');
$direction = safeParam(strtolower((string) ($_GET['direction'] ?? 'asc')), ['asc', 'desc'], 'asc');

try {
    // Open the database connection and load all artists using the selected sorting.
    $db = new dbaccess();
    $db->connect();

    $artistRepository = new artistRepository($db);
    $artists = $artistRepository->getAllSorted($sort, $direction);
} catch (Throwable $e) {
    // Stop execution and show the error message during development.
    die($e->getMessage());
}

// Render the shared header after all page data has been prepared.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Page heading for the artist browse page. -->
    <section class="page-heading">
        <h1>Künstler durchsuchen</h1>
        <p>Entdecken sie Künstler*innen</p>
    </section>

    <!-- Sorting form for changing the artist list order. -->
    <section class="sort-panel" aria-label="Sortierung der Künstler">
        <form method="get" action="<?= e(base_url('pages/browse-artists.php')); ?>">
            <label for="sort">Sortieren nach</label>
            <select id="sort" name="sort">
                <option value="firstName" <?= $sort === 'firstName' ? 'selected' : ''; ?>>Vorname</option>
                <option value="lastName" <?= $sort === 'lastName' ? 'selected' : ''; ?>>Nachname</option>
            </select>

            <label for="direction">Richtung</label>
            <select id="direction" name="direction">
                <option value="asc" <?= $direction === 'asc' ? 'selected' : ''; ?>>Aufsteigend</option>
                <option value="desc" <?= $direction === 'desc' ? 'selected' : ''; ?>>Absteigend</option>
            </select>

            <button type="submit">Anwenden</button>
        </form>
    </section>

<?php if (empty($artists)): ?>
    <!-- Empty-state message shown when no artists are available. -->
    <section class="message">
        Es wurden keine Künstler gefunden.
    </section>
<?php else: ?>
    <!-- Responsive grid containing all artist cards. -->
    <section class="artist-card-grid" aria-label="Liste der Künstler">
        <?php foreach ($artists as $artist): ?>
            <?php
            // Prepare display values for the current artist card.
            $artistId = $artist->getId();

            $artistName = trim(
                    ($artist->getFirstName() ?? '') . ' ' . ($artist->getLastName() ?? '')
            );

            // Use a fallback label if both first and last name are missing.
            if ($artistName === '') {
                $artistName = 'Unbekannter Künstler';
            }

            // Build the artist image URL or use a placeholder image as fallback.
            $imageFileName = $artist->getImagefilename();

            $imageUrl = $imageFileName
                    ? artistImageUrl($imageFileName, 'square-small')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Single artist card. -->
            <article class="artist-card-link-wrapper">
                <a href="<?= e(artistDetailUrl($artistId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($artistName); ?>"
                            class="artist-card-image"
                    >
                </a>

                <div class="artist-card-content">
                    <h2>
                        <a href="<?= e(artistDetailUrl($artistId)); ?>">
                            <?= e($artistName); ?>
                        </a>
                    </h2>

                    <div class="result-actions">
                        <a class="btn btn-sm btn-primary" href="<?= e(artistDetailUrl($artistId)); ?>">
                            Ansehen
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php
// Render the shared footer.
require_once __DIR__ . '/../includes/footer.php';
?>