<?php
// Initialisierung, gemeinsame Hilfsfunktionen und benötigte Repositories laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Subject-ID aus der URL lesen und in eine Ganzzahl umwandeln.
$subjectId = (int) ($_GET['id'] ?? 0);

// Frühzeitig abbrechen, wenn keine gültige Subject-ID übergeben wurde.
if ($subjectId <= 0) {
    $pageTitle = 'Thema nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Datenbankverbindung öffnen.
    $db = new dbaccess();
    $db->connect();

    // Rohdaten des Subjects laden, damit auch optionale Datenbankspalten verfügbar sind.
    $stmt = $db->preparedStatement("SELECT * FROM subjects WHERE SubjectID = :id");
    $stmt->execute(['id' => $subjectId]);
    $subjectRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Benutzerfreundliche Fehlermeldung ausgeben, falls kein Subject gefunden wurde.
    if (!$subjectRow) {
        $pageTitle = 'Thema nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Thema nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Alle Kunstwerke laden, die dem aktuellen Subject zugeordnet sind.
    $artworks = (new artworkRepository($db))->getForSubject($subjectId);

} catch (Throwable $e) {
    // Fallback-Fehlerseite anzeigen, falls das Laden der Daten fehlschlägt.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-subject.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Anzeigewerte für die Subjectdetailseite vorbereiten.
$subjectName = (string) ($subjectRow['SubjectName'] ?? '');

// Externen Subjectlink aus möglichen Datenbankspalten lesen.
$subjectLink = (string) (
        $subjectRow['SubjectLink'] ??
        $subjectRow['subjectlink'] ??
        $subjectRow['Subjectlink'] ??
        $subjectRow['Link']        ??
        ''
);

// Hauptbild des Subjects vorbereiten.
$subjectPhoto = subjectImageUrl($subjectId);

// Finalen Seitentitel setzen und Header laden.
$pageTitle = $subjectName . ' · Thema';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Hauptbereich der Subjectdetailseite mit Bild und Informationen. -->
    <section class="subject-detail-layout">

        <!-- Bildbereich des Subjects. -->
        <div class="subject-image-panel">
            <img src="<?= e($subjectPhoto); ?>" alt="<?= e($subjectName); ?>">
        </div>

        <!-- Informationsbereich mit externem Link. -->
        <div class="subject-info-panel">
            <h1><?= e($subjectName); ?></h1>

            <!-- Tabelle mit Subjectdetails. -->
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

    <!-- Zugeordnete Kunstwerke des aktuellen Subjects. -->
    <section class="mt-5">
        <h2>Kunstwerke zum Thema '<?= e($subjectName); ?>'</h2>

        <?php if (empty($artworks)): ?>
            <!-- Hinweis, falls dem Subject keine Kunstwerke zugeordnet sind. -->
            <p class="text-muted">Keine Kunstwerke für dieses Thema gefunden.</p>
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
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>