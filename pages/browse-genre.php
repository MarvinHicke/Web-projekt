<?php
// Enable detailed error output during development.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set the page title before loading the shared header.
$pageTitle = 'Genre durchsuchen';

// Load application initialization, shared helpers, and the genre repository.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

try {
    // Open the database connection and load all genres.
    $db = new dbaccess();
    $db->connect();

    $genreRepository = new genreRepository($db);
    $genres = $genreRepository->findAll();
} catch (Throwable $e) {
    // Stop execution and show the error message during development.
    die($e->getMessage());
}

// Render the shared header after all page data has been prepared.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Page heading for the genre browse page. -->
    <section class="page-heading">
        <h1>Genres durchsuchen</h1>
        <p>Entdecken Sie Kunstrichtungen und Epochen.</p>
    </section>

<?php if (empty($genres)): ?>
    <!-- Empty-state message shown when no genres are available. -->
    <section class="message">
        Es wurden keine Genres gefunden.
    </section>
<?php else: ?>
    <!-- Responsive grid containing all genre cards. -->
    <section class="genre-card-grid" aria-label="Liste der Genres">
        <?php foreach ($genres as $genre): ?>
            <?php
            // Prepare display values for the current genre card.
            $genreId = $genre->getGenreID();
            $genreName = $genre->getGenreName();
            $era = $genre->getEra();
            $description = $genre->getDescription();

            // Use a fallback label if the genre name is missing.
            if ($genreName === '') {
                $genreName = 'Unbekanntes Genre';
            }

            // Build the genre image URL or use a placeholder image as fallback.
            $imageFileName = $genre->getImagefilename();

            $imageUrl = $imageFileName
                    ? genreImageUrl($imageFileName, 'square-medium')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Single genre card. -->
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
// Render the shared footer.
require_once __DIR__ . '/../includes/footer.php';
?>