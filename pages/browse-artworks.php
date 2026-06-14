<?php
/**
 * Seite zum Durchsuchen aller Kunstwerke (UC06).
 *
 * Zeigt alle Kunstwerke aus der Datenbank in einem Kartengitter an.
 * Die Liste kann nach Titel (Standard), Künstler oder Jahr
 * in auf- oder absteigender Reihenfolge sortiert werden.
 * Jede Karte verlinkt auf die Einzelansicht des Kunstwerks (UC12).
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$pageTitle = 'Kunstwerke durchsuchen';

// Sortierparameter aus der URL lesen und gegen erlaubte Werte absichern
$sort      = safeParam((string) ($_GET['sort']      ?? 'title'), ['title', 'artist', 'year'], 'title');
$direction = safeParam(strtolower((string) ($_GET['direction'] ?? 'asc')), ['asc', 'desc'], 'asc');

try {
    $db = new dbaccess();
    $db->connect();
    $artworkRepository = new artworkRepository($db);

    // Alle Kunstwerke sortiert aus der DB laden (JOIN mit Artists für Künstlername)
    $artworks = $artworkRepository->getAllSorted($sort, $direction);
} catch (Exception $e) {
    // Bei Datenbankfehler leere Liste verwenden, damit die Seite nicht abstürzt
    $artworks = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <h1>Kunstwerke durchsuchen</h1>
    <p>Hier finden Sie alle Kunstwerke. Die Liste kann nach Titel, Künstler oder Jahr sortiert werden.</p>
</section>

<!-- Sortierformular (UC06): GET-Methode, damit die Sortierung in der URL sichtbar bleibt -->
<section class="sort-panel" aria-label="Sortierung der Kunstwerke">
    <form method="get" action="<?= e(base_url('pages/browse-artworks.php')); ?>">
        <label for="sort">Sortieren nach</label>
        <select id="sort" name="sort">
            <option value="title"  <?= $sort === 'title'  ? 'selected' : ''; ?>>Titel</option>
            <option value="artist" <?= $sort === 'artist' ? 'selected' : ''; ?>>Künstler</option>
            <option value="year"   <?= $sort === 'year'   ? 'selected' : ''; ?>>Jahr</option>
        </select>

        <label for="direction">Richtung</label>
        <select id="direction" name="direction">
            <option value="asc"  <?= $direction === 'asc'  ? 'selected' : ''; ?>>Aufsteigend</option>
            <option value="desc" <?= $direction === 'desc' ? 'selected' : ''; ?>>Absteigend</option>
        </select>

        <button type="submit">Anwenden</button>
    </form>
</section>

<?php if (empty($artworks)): ?>
    <!-- Leer-Zustand: keine Kunstwerke gefunden (UC15: kein Absturz bei leerer DB) -->
    <section class="message">
        Es wurden keine Kunstwerke gefunden.
    </section>
<?php else: ?>
    <section class="artwork-card-grid" aria-label="Liste der Kunstwerke">
        <?php foreach ($artworks as $artwork): ?>
            <?php
            $artworkId   = $artwork->getArtworkid();
            $title       = $artwork->getTitle();

            // Künstlername aus Vor- und Nachname zusammensetzen
            $artistName  = trim(
                ($artwork->getFirstName() ?? '') . ' ' . ($artwork->getLastName() ?? '')
            );
            $year        = $artwork->getYearofwork();
            $imageFileName = $artwork->getImagefilename();
            ?>

            <article class="artwork-card-link-wrapper">
                <!-- Bild als Link zur Einzelansicht (UC06 → UC12) -->
                <a class="artwork-card-link" href="<?= e(artworkDetailUrl($artworkId)); ?>">
                    <!-- square-medium für bessere Bildqualität im Raster (UC15) -->
                    <img
                        src="<?= e(artworkImageUrl($imageFileName, 'square-medium')); ?>"
                        alt="<?= e($title); ?>"
                        class="artwork-card-image"
                    >
                </a>
                <div class="artwork-card-content">
                    <h2><a href="<?= e(artworkDetailUrl($artworkId)); ?>"><?= e($title); ?></a></h2>
                    <p><strong>Künstler:</strong> <?= e($artistName !== '' ? $artistName : 'Unbekannt'); ?></p>
                    <p><strong>Jahr:</strong> <?= e($year !== '' ? (string)$year : 'Unbekannt'); ?></p>
                    <a class="btn btn-sm btn-primary" href="<?= e(artworkDetailUrl($artworkId)); ?>">Einzelansicht öffnen</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
