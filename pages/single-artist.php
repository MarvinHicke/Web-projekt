<?php
// Initialisierung, gemeinsame Hilfsfunktionen und benötigte Repositories laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

// Künstler-ID aus der URL lesen und in eine Ganzzahl umwandeln.
$artistId = (int) ($_GET['id'] ?? 0);

// Frühzeitig abbrechen, wenn keine gültige Künstler-ID übergeben wurde.
if ($artistId <= 0) {
    $pageTitle = 'Künstler nicht gefunden';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ungültige ID</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Datenbankverbindung öffnen.
    $db = new dbaccess();
    $db->connect();

    // Künstlerobjekt laden, um Model-Getter für Bild- und Maßangaben verwenden zu können.
    $artistObj = (new artistRepository($db))->getById($artistId);

    // Rohdaten des Künstlers laden, damit auch optionale Datenbankspalten verfügbar sind.
    $stmt = $db->preparedStatement("SELECT * FROM artists WHERE ArtistID = :id");
    $stmt->execute(['id' => $artistId]);
    $artistRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Benutzerfreundliche Fehlermeldung ausgeben, falls kein Künstler gefunden wurde.
    if (!$artistRow) {
        $pageTitle = 'Künstler nicht gefunden';

        require_once __DIR__ . '/../includes/header.php';

        echo '<section class="page-heading"><h1>Künstler nicht gefunden</h1>'
                . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück zur Übersicht</a></section>';

        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Alle Kunstwerke laden, die dem aktuellen Künstler zugeordnet sind.
    $artworks = (new artworkRepository($db))->getForArtist($artistId);

} catch (Throwable $e) {
    // Fallback-Fehlerseite anzeigen, falls das Laden der Daten fehlschlägt.
    $pageTitle = 'Fehler';

    require_once __DIR__ . '/../includes/header.php';

    echo '<section class="page-heading"><h1>Ladefehler</h1>'
            . '<a class="button-link" href="' . e(base_url('pages/browse-artists.php')) . '">Zurück</a></section>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Anzeigewerte für die Künstlerdetailseite vorbereiten.
$firstName   = (string) ($artistRow['FirstName'] ?? '');
$lastName    = (string) ($artistRow['LastName'] ?? '');
$fullName    = trim($firstName . ' ' . $lastName);
$nationality = (string) ($artistRow['Nationality'] ?? $artistRow['nationality'] ?? '');

// Beschreibung/Biografie aus möglichen Datenbankspalten lesen.
$details = cleanHtml(
        $artistRow['Details']     ??
        $artistRow['details']     ??
        $artistRow['Description'] ??
        ''
);

// Externen Künstlerlink aus möglichen Datenbankspalten lesen.
$artistLink = (string) (
        $artistRow['ArtistLink'] ??
        $artistRow['artistlink'] ??
        $artistRow['Artistlink'] ??
        $artistRow['Link']       ??
        ''
);

// Geburts- und Sterbejahr aus möglichen Datenbankspalten lesen.
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

// Lebensdaten als lesbaren Text zusammensetzen.
if ($birthYear !== '' && $deathYear !== '') {
    $dateString = $birthYear . ' – ' . $deathYear;
} elseif ($birthYear !== '') {
    $dateString = $birthYear;
} else {
    $dateString = '';
}

// Bild- und Maßangaben für den Bildbereich vorbereiten.
$imageFileName = $artistObj->getImagefilename();
$mediumImage   = artistImageUrl($imageFileName, 'medium');

// Prüfen, ob der aktuelle Künstler bereits in den Session-Favoriten gespeichert ist.
$isFavorited = isset($_SESSION['favorites']['artists'])
        && in_array($artistId, array_map('intval', $_SESSION['favorites']['artists']), true);

// Finalen Seitentitel setzen und Header laden.
$pageTitle = $fullName . ' · Künstler';
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Hauptbereich der Künstlerdetailseite mit Bild und Informationen. -->
    <section class="artist-detail-layout">

        <!-- Bildbereich -->
        <div class="artist-image-panel">
            <img
                    src="<?= e($mediumImage); ?>"
                    alt="<?= e($fullName); ?>"
                    class="artwork-main-image img-fluid"
            >
            <small class="d-block text-muted mt-1"></small>
        </div>

        <!-- Informationsbereich mit Biografie, Favoritenbutton und Metadaten. -->
        <div class="artist-info-panel">
            <h1><?= e($fullName); ?></h1>

            <?php if ($details !== ''): ?>
                <p class="artist-bio"><?= e($details); ?></p>
            <?php endif; ?>

            <!-- Sessionbasierter Favoritenbutton für Künstler. -->
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

            <!-- Tabelle mit Künstlerdetails. -->
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

    <!-- Zugeordnete Kunstwerke des aktuellen Künstlers. -->
    <section class="mt-5">
        <h2>Kunstwerke von <?= e($fullName); ?></h2>

        <?php if (empty($artworks)): ?>
            <!-- Hinweis, falls dem Künstler keine Kunstwerke zugeordnet sind. -->
            <p class="text-muted">Keine Kunstwerke für diesen Künstler gefunden.</p>
        <?php else: ?>
            <!-- Responsives Raster mit Kunstwerkkarten. -->
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
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>