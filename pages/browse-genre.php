<?php
// Detaillierte Fehlerausgabe während der Entwicklung aktivieren.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Seitentitel vor dem Laden des Headers setzen.
$pageTitle = 'Genre durchsuchen';

// Initialisierung, gemeinsame Hilfsfunktionen und Repository laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

try {
    // Datenbankverbindung öffnen und Genres laden.
    $db = new dbaccess();
    $db->connect();

    $genreRepository = new genreRepository($db);
    $genres = $genreRepository->findAll();
} catch (Throwable $e) {
    // Fehler während der Entwicklung direkt ausgeben.
    die($e->getMessage());
}

// Gemeinsamen Header einbinden, nachdem die Seitendaten vorbereitet wurden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Seitenüberschrift für die Genreübersicht. -->
    <section class="page-heading">
        <h1>Genres durchsuchen</h1>
        <p>Entdecken Sie Kunstrichtungen und ihre Epochen.</p>
    </section>

<?php if (empty($genres)): ?>
    <!-- Hinweis, falls keine Genres vorhanden sind. -->
    <section class="message">
        Es wurden keine Genres gefunden.
    </section>
<?php else: ?>
    <!-- Kartenraster mit allen Genres. -->
    <section class="genre-card-grid" aria-label="Liste der Genres">
        <?php foreach ($genres as $genre): ?>
            <?php
            // Anzeigewerte für die aktuelle Genrekarte vorbereiten.
            $genreId = $genre->getGenreID();
            $genreName = $genre->getGenreName();
            $era = $genre->getEra();
            $description = $genre->getDescription();

            // Fallback verwenden, falls kein Genrename vorhanden ist.
            if ($genreName === '') {
                $genreName = 'Unbekanntes Genre';
            }

            // Genrebild laden oder Platzhalter verwenden.
            $imageFileName = $genre->getImagefilename();

            $imageUrl = $imageFileName
                    ? genreImageUrl($imageFileName, 'square-medium')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Einzelne Genrekarte. -->
            <article class="genre-card-link-wrapper">
                <a href="<?= e(genreDetailUrl($genreId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($genreName); ?>"
                            class="genre-card-image"
                    >
                </a>

                <div class="genre-card-content">
                    <h2>
                        <a href="<?= e(genreDetailUrl($genreId)); ?>">
                            <?= e($genreName); ?>
                        </a>
                    </h2>

                    <p>
                        <strong>Era:</strong>
                        <?= e($era !== '' ? $era : 'Unbekannt'); ?>
                    </p>

                    <div class="result-actions">
                        <a class="btn btn-sm btn-primary" href="<?= e(genreDetailUrl($genreId)); ?>">
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