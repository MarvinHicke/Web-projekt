<?php
// Detaillierte Fehlerausgabe während der Entwicklung aktivieren.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Seitentitel vor dem Laden des Headers setzen.
$pageTitle = 'Künstler durchsuchen';

// Initialisierung, gemeinsame Hilfsfunktionen und Repository laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

// Sortierparameter aus der URL lesen und auf erlaubte Werte begrenzen.
$sort = safeParam((string) ($_GET['sort'] ?? 'firstName'), ['firstName', 'lastName'], 'firstName');
$direction = safeParam(strtolower((string) ($_GET['direction'] ?? 'asc')), ['asc', 'desc'], 'asc');

try {
    // Datenbankverbindung öffnen und Künstler sortiert laden.
    $db = new dbaccess();
    $db->connect();

    $artistRepository = new artistRepository($db);
    $artists = $artistRepository->getAllSorted($sort, $direction);
} catch (Throwable $e) {
    // Fehler während der Entwicklung direkt ausgeben.
    die($e->getMessage());
}

// Gemeinsamen Header einbinden, nachdem die Seitendaten vorbereitet wurden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Seitenüberschrift für die Künstlerübersicht. -->
    <section class="page-heading">
        <h1>Künstler durchsuchen</h1>
        <p>Hier finden Sie alle Künstler. Die Liste kann nach Vor- oder Nachname sortiert werden.
        </p>
    </section>

    <!-- Formular zur Sortierung der Künstlerliste. -->
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
    <!-- Hinweis, falls keine Künstler vorhanden sind. -->
    <section class="message">
        Es wurden keine Künstler gefunden.
    </section>
<?php else: ?>
    <!-- Kartenraster mit allen Künstlern. -->
    <section class="artist-card-grid" aria-label="Liste der Künstler">
        <?php foreach ($artists as $artist): ?>
            <?php
            // Anzeigewerte für die aktuelle Künstlerkarte vorbereiten.
            $artistId = $artist->getId();

            $artistName = trim(
                    ($artist->getFirstName() ?? '') . ' ' . ($artist->getLastName() ?? '')
            );

            // Fallback verwenden, falls kein Name vorhanden ist.
            if ($artistName === '') {
                $artistName = 'Unbekannter Künstler';
            }

            // Künstlerbild laden oder Platzhalter verwenden.
            $imageFileName = $artist->getImagefilename();

            $imageUrl = $imageFileName
                    ? artistImageUrl($imageFileName, 'square-small')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Einzelne Künstlerkarte. -->
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
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>