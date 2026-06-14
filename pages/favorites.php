<?php
/**
 * Favoritenübersicht für UC19.
 *
 * Liest favorisierte Kunstwerke und Künstler aus der Session und zeigt die
 * zugehörigen Datenbankeinträge mit Entfernen-Links an.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

// Seitentitel für die Favoritenübersicht setzen.
$pageTitle = 'Favoriten';

// Session starten, falls sie noch nicht aktiv ist.
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

// Sessionbasierte Favoritenstruktur initialisieren, die von UC18 und UC19 verwendet wird.
if (!isset($_SESSION['favorites']))
{
    $_SESSION['favorites'] = [];
}

// Favoritenliste für Kunstwerke initialisieren, falls sie noch nicht existiert.
if (!isset($_SESSION['favorites']['artworks']))
{
    $_SESSION['favorites']['artworks'] = [];
}

// Favoritenliste für Künstler initialisieren, falls sie noch nicht existiert.
if (!isset($_SESSION['favorites']['artists']))
{
    $_SESSION['favorites']['artists'] = [];
}

// Favoriten-IDs aus der Session lesen.
$favoriteArtworkIds = $_SESSION['favorites']['artworks'];
$favoriteArtistIds = $_SESSION['favorites']['artists'];

// Ergebnislisten für die geladenen Datenbankobjekte vorbereiten.
$favoriteArtworks = [];
$favoriteArtists = [];

// Datenbankeinträge nur laden, wenn mindestens ein Favorit vorhanden ist.
if (!empty($favoriteArtworkIds) || !empty($favoriteArtistIds))
{
    // Datenbankverbindung und benötigte Repositories initialisieren.
    $db = new dbaccess();
    $db->connect();

    $artworkRepository = new artworkRepository($db);
    $artistRepository = new artistRepository($db);

    // Favorisierte Kunstwerke anhand ihrer IDs aus der Datenbank laden.
    foreach ($favoriteArtworkIds as $artworkId)
    {
        $artwork = $artworkRepository->getById((int) $artworkId);

        if ($artwork !== null)
        {
            $favoriteArtworks[] = $artwork;
        }
    }

    // Favorisierte Künstler anhand ihrer IDs aus der Datenbank laden.
    foreach ($favoriteArtistIds as $artistId)
    {
        $artist = $artistRepository->getById((int) $artistId);

        if ($artist !== null)
        {
            $favoriteArtists[] = $artist;
        }
    }
}

// Gemeinsamen Header einbinden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Seitenüberschrift der Favoritenübersicht. -->
    <section class="page-heading">
        <p class="eyebrow">Ihre persönliche Auswahl</p>
        <h1>Favoriten</h1>
        <p class="mb-0">Hier sehen Sie Ihre favorisierten Künstler und Kunstwerke.</p>
    </section>

    <!-- Zweispaltige Ergebnisansicht für Künstler- und Kunstwerkfavoriten. -->
    <section class="result-grid" aria-label="Favoritenlisten">
        <div class="result-column">
            <h2>Favorisierte Künstler</h2>

            <?php if (empty($favoriteArtists)): ?>
                <?php
                // Hinweis anzeigen, wenn keine Künstler favorisiert wurden.
                $alertType = 'info';
                $alertMessage = 'Du hast noch keine Künstler favorisiert.';
                include __DIR__ . '/../components/alert-box.php';
                ?>
            <?php else: ?>
                <?php foreach ($favoriteArtists as $artist): ?>
                    <?php
                    // Anzeigewerte für die aktuelle Künstlerkarte vorbereiten.
                    $artistId = (int) $artist->getId();
                    $artistName = trim((string) $artist->getFirstName() . ' ' . (string) $artist->getLastName());
                    ?>

                    <article class="mini-card">
                        <img
                                src="<?= e(artistImageUrl($artistId, 'medium')); ?>"
                                alt="<?= e($artistName); ?>"
                        >

                        <div>
                            <h3>
                                <a href="<?= e(artistDetailUrl($artistId)); ?>">
                                    <?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?>
                                </a>
                            </h3>

                            <p>Artist ID: <?= e((string) $artistId); ?></p>

                            <a
                                    class="btn btn-danger btn-sm mt-2"
                                    href="<?= e(base_url('pages/remove-favorite.php') . '?type=artist&id=' . urlencode((string) $artistId)); ?>"
                            >
                                Aus Favoriten entfernen
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="result-column">
            <h2>Favorisierte Kunstwerke</h2>

            <?php if (empty($favoriteArtworks)): ?>
                <?php
                // Hinweis anzeigen, wenn keine Kunstwerke favorisiert wurden.
                $alertType = 'info';
                $alertMessage = 'Du hast noch keine Kunstwerke favorisiert.';
                include __DIR__ . '/../components/alert-box.php';
                ?>
            <?php else: ?>
                <?php foreach ($favoriteArtworks as $artwork): ?>
                    <?php
                    // Anzeigewerte für die aktuelle Kunstwerkkarte vorbereiten.
                    $artworkId = (int) $artwork->getArtworkid();
                    $title = (string) $artwork->getTitle();
                    $year = (string) $artwork->getYearofwork();
                    $imageFileName = (string) $artwork->getImagefilename();
                    $artistName = trim((string) $artwork->getFirstName() . ' ' . (string) $artwork->getLastName());
                    ?>

                    <article class="mini-card">
                        <img
                                src="<?= e(artworkImageUrl($imageFileName, 'medium')); ?>"
                                alt="<?= e($title); ?>"
                        >

                        <div>
                            <h3>
                                <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                                    <?= e($title !== '' ? $title : 'Unbekanntes Kunstwerk'); ?>
                                </a>
                            </h3>
                            <p><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></p>
                            <p><?= e($year !== '' ? $year : 'Jahr unbekannt'); ?></p>

                            <a
                                    class="btn btn-danger btn-sm mt-2"
                                    href="<?= e(base_url('pages/remove-favorite.php') . '?type=artwork&id=' . urlencode((string) $artworkId)); ?>"
                            >
                                Aus Favoriten entfernen
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>