<?php
/**
 * Startseite der Art Gallery Webanwendung.
 *
 * Diese Seite dient als Einstiegspunkt für alle Besucher.
 * Sie zeigt ein Bootstrap-Karussell mit den am besten bewerteten Kunstwerken,
 * ein Login-Formular für nicht angemeldete Nutzer sowie drei Datenboxen:
 * Top-Werke, meistbewertete Künstler und neueste Bewertungen.
 *
 * Die Datenboxen sind als ausgelagerte Funktionen in eigenen Dateien
 * eingebunden (UC02-Anforderung).
 */

$pageTitle = 'Startseite · Art Gallery';

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/repositories/artworkRepository.php';
require_once __DIR__ . '/repositories/artistRepository.php';
require_once __DIR__ . '/repositories/reviewRepository.php';

// Ausgelagerte Boxen-Funktionen einbinden (UC02: Boxen als Funktionen in eigenen Dateien)
require_once __DIR__ . '/includes/boxes/top-works-box.php';
require_once __DIR__ . '/includes/boxes/most-reviewed-artists-box.php';
require_once __DIR__ . '/includes/boxes/most-recent-reviews-box.php';

// Standardwerte: leere Listen, falls die DB nicht erreichbar ist
$topArtworks         = [];
$carouselArtworks    = [];
$mostReviewedArtists = [];
$latestReviews       = [];

try {
    $db = new dbaccess();
    $db->connect();

    $artworkRepo = new artworkRepository($db);
    $artistRepo  = new artistRepository($db);
    $reviewRepo  = new reviewRepository($db);

    // 30 Kandidaten abrufen, damit nach der Bildfilterung genug für das Karussell übrig bleiben
    $topArtworkCandidates = $artworkRepo->getTopArtworks(30);

    // Für die Boxen werden die ersten 3 Ergebnisse verwendet
    $topArtworks = array_slice($topArtworkCandidates, 0, 3);

    /**
     * Karussell-Filter: Nur Kunstwerke anzeigen, für die tatsächlich
     * eine Bilddatei auf dem Server vorhanden ist.
     * Es werden maximal 5 Einträge aufgenommen.
     */
    $carouselArtworks = [];
    foreach ($topArtworkCandidates as $work) {
        $fileName = trim((string) ($work['ImageFileName'] ?? ''));
        if ($fileName === '') continue;

        // .jpg-Endung ergänzen, falls sie im Dateinamen fehlt
        if (!str_ends_with(strtolower($fileName), '.jpg')) {
            $fileName .= '.jpg';
        }

        // Datei in verschiedenen Größenordnern suchen
        foreach (['large', 'medium', 'small', 'square-small'] as $size) {
            if (file_exists(__DIR__ . '/images/works/' . $size . '/' . $fileName)) {
                $carouselArtworks[] = $work;
                break; // Sobald eine Größe gefunden wurde, nächstes Kunstwerk
            }
        }

        // Maximale Karussell-Größe: 5 Einträge
        if (count($carouselArtworks) >= 5) break;
    }

    $mostReviewedArtists = $artistRepo->getMostReviewedArtists(3);

    // Neueste Bewertungen mit Kunstwerkname per JOIN (eigene Methode, gibt Arrays zurück)
    $latestReviews = $reviewRepo->getLatestReviewsWithDetails(3);

} catch (Exception $e) {
    // Datenbankfehler: Seite wird mit leeren Listen gerendert, kein Absturz
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO-BEREICH ===== -->
<section class="hero">
    <div>
        <p class="eyebrow">Willkommen bei</p>
        <h1>Art Gallery</h1>
        <p>Entdecken Sie Kunstwerke, Künstler, Bewertungen und Galerien.</p>
        <a class="button-link" href="<?= e(base_url('pages/browse-artworks.php')); ?>">Kunstwerke durchsuchen</a>
    </div>
</section>

<!-- Login-Formular nur für nicht angemeldete Benutzer (UC22) -->
<?php if (!isLoggedIn()): ?>
<section class="home-login-panel" aria-labelledby="home-login-title">
    <div>
        <p class="eyebrow">Persönlicher Bereich</p>
        <h2 id="home-login-title">Anmelden</h2>
        <p class="mb-0">Melden Sie sich an, um Favoriten und Bewertungen zu verwalten.</p>
    </div>
    <form method="post" action="<?= e(base_url('pages/login.php')); ?>" class="home-login-form">
        <div>
            <label class="form-label" for="home-login-email">E-Mail</label>
            <input class="form-control" type="email" id="home-login-email" name="email"
                   autocomplete="email" required maxlength="100">
        </div>
        <div>
            <label class="form-label" for="home-login-password">Passwort</label>
            <input class="form-control" type="password" id="home-login-password" name="password"
                   autocomplete="current-password" required minlength="8">
        </div>
        <button class="btn btn-primary" type="submit">Anmelden</button>
    </form>
</section>
<?php endif; ?>

<!-- ===== BOOTSTRAP-KARUSSELL (UC02) ===== -->
<?php if (!empty($carouselArtworks)): ?>
<section class="carousel-section" aria-label="Vorgestellte Kunstwerke">
    <div id="artworkCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

        <!-- Indikatorpunkte: einer pro Slide -->
        <div class="carousel-indicators">
            <?php foreach ($carouselArtworks as $i => $work): ?>
                <button
                    type="button"
                    data-bs-target="#artworkCarousel"
                    data-bs-slide-to="<?= $i; ?>"
                    <?= $i === 0 ? 'class="active" aria-current="true"' : ''; ?>
                    aria-label="Slide <?= $i + 1; ?>"
                ></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <?php foreach ($carouselArtworks as $i => $work): ?>
                <?php
                $id            = (int)    ($work['ArtWorkID']     ?? 0);
                $title         = (string) ($work['Title']          ?? 'Unbekanntes Kunstwerk');
                $imageFileName = (string) ($work['ImageFileName']  ?? '');
                $rating        = $work['AvgRating'] ?? null;
                ?>
                <!-- Jeder Slide ist ein klickbarer Link zur Einzelansicht (UC02) -->
                <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                    <a href="<?= e(artworkDetailUrl($id)); ?>">
                        <img
                            src="<?= e(artworkImageUrl($imageFileName, 'large')); ?>"
                            class="d-block w-100 carousel-img"
                            alt="<?= e($title); ?>"
                        >
                    </a>
                    <div class="carousel-caption d-none d-md-block"
                         style="background: rgba(0,0,0,0.45); border-radius: 8px; padding: 0.75rem 1.25rem; bottom: 2rem;">
                        <h5 style="margin-bottom: 0.25rem;"><?= e($title); ?></h5>
                        <?php if ($rating !== null): ?>
                            <p style="margin-bottom: 0.5rem; opacity: 0.9;">
                                Bewertung: <?= e(number_format((float)$rating, 1, ',', '.')); ?>/5 ★
                            </p>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-primary" href="<?= e(artworkDetailUrl($id)); ?>">Ansehen</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigationsschaltflächen des Karussells -->
        <button class="carousel-control-prev" type="button" data-bs-target="#artworkCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Zurück</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#artworkCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Weiter</span>
        </button>
    </div>
</section>
<?php endif; ?>

<!-- ===== DREI DATENBOXEN (UC02) ===== -->
<!-- Jede Box ist eine ausgelagerte Funktion in einer eigenen Datei -->
<section class="three-box-grid" aria-label="Datenboxen auf der Startseite"
         style="margin-top: 1rem; padding-top: 1rem;">
    <?php renderTopWorksBox($topArtworks); ?>
    <?php renderMostReviewedArtistsBox($mostReviewedArtists); ?>
    <?php renderMostRecentReviewsBox($latestReviews); ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
