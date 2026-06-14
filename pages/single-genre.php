<?php
// Initialisierung, gemeinsame Hilfsfunktionen und benötigte Repositories laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Genre-ID aus der URL lesen und in eine Ganzzahl umwandeln.
$genreId = (int) ($_GET['id'] ?? 0);

// Frühzeitig abbrechen, wenn keine gültige Genre-ID übergeben wurde.
if ($genreId <= 0) {
    $pageTitle = 'Genre nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Datenbankverbindung öffnen.
    $db = new dbaccess();
    $db->connect();

    // Genreobjekt laden.
    $genreRepository = new genreRepository($db);
    $genreObj = $genreRepository->getById($genreId);

    // Rohdaten des Genres laden, damit auch optionale Datenbankspalten verfügbar sind.
    $stmt = $db->preparedStatement("SELECT * FROM genres WHERE GenreID = :id");
    $stmt->execute(['id' => $genreId]);
    $genreRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Benutzerfreundliche Fehlermeldung ausgeben, falls kein Genre gefunden wurde.
    if (!$genreRow) {
        $pageTitle = 'Genre nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Genre nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Alle Kunstwerke laden, die dem aktuellen Genre zugeordnet sind.
    $artworks = (new artworkRepository($db))->getForGenre($genreId);

} catch (Throwable $e) {
    // Fallback-Fehlerseite anzeigen, falls das Laden der Daten fehlschlägt.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-genre.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Anzeigewerte für die Genredetailseite vorbereiten.
$genreName = (string) ($genreRow['GenreName'] ?? '');
$genreEra = (string) ($genreRow['Era'] ?? '');
$genreDescription = cleanHtml((string) ($genreRow['Description'] ?? ''));

// Externen Genrelink aus möglichen Datenbankspalten lesen.
$genreLink = (string) (
        $genreRow['GenreLink'] ??
        $genreRow['Genrelink'] ??
        $genreRow['genrelink'] ??
        $genreRow['Link']      ??
        ''
);

// Hauptbild des Genres vorbereiten.
$genrePhoto = genreImageUrl($genreId);

// Finalen Seitentitel setzen und Header laden.
$pageTitle = $genreName . ' · Genre';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Hauptbereich der Genredetailseite mit Bild und Informationen. -->
    <section class="genre-detail-layout">

        <!-- Bildbereich des Genres. -->
        <div class="genre-image-panel">
            <img src="<?= e($genrePhoto); ?>" alt="<?= e($genreName); ?>">
        </div>

        <!-- Informationsbereich mit Beschreibung und Metadaten. -->
        <div class="genre-info-panel">
            <h1><?= e($genreName); ?></h1>

            <?php if ($genreDescription !== ''): ?>
                <p><strong>Beschreibung:</strong> <?= e($genreDescription); ?></p>
            <?php endif; ?>

            <!-- Tabelle mit Genredetails. -->
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

    <!-- Zugeordnete Kunstwerke des aktuellen Genres. -->
    <section class="mt-5">
        <h2>Kunstwerke des Genre '<?= e($genreName); ?>'</h2>

        <?php if (empty($artworks)): ?>
            <!-- Hinweis, falls dem Genre keine Kunstwerke zugeordnet sind. -->
            <p class="text-muted">Keine Kunstwerke für dieses Genre gefunden.</p>
        <?php else: ?>
            <!-- Responsives Raster mit Kunstwerkkarten. -->
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <?php foreach ($artworks as $artwork): ?>
                    <?php
                    // Anzeigewerte für die aktuelle Kunstwerkkarte vorbereiten.
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
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>