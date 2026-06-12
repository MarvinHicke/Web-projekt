<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Genre durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$sort = safeParam((string) ($_GET['sort'] ?? 'era'), ['era', 'genreName'], 'eraName');

try {
    $db = new dbaccess();
    $db->connect();
    $genreRepository = new genreRepository($db);
    $genres = $genreRepository->findAll();
} catch (Throwable $e) {
    die($e->getMessage());
}

$_SESSION["favorites"] ??= [];
$_SESSION["favorites"]["genres"] ??= [];

$favoriteGenresIds = array_map('intval', $_SESSION["favorites"]["genres"]);

require_once __DIR__.'/../includes/header.php';

?>
    <section class="page-heading">
        <h1>Genres durchsuchen</h1>
        <p>Entdecken sie Genre</p>
    </section>


<?php if (empty($genres)): ?>
    <section class="message">
        Es wurde kein Genre gefunden.
    </section>
<?php else: ?>
    <section class="genre-card-grid" aria-label="Liste der Genres">
        <?php foreach ($genres as $genre): ?>
            <?php
            $genreId = $genre->getGenreID();

            $genreName = $genre->getGenreName();

            $era = $genre->getEra();

            $description = $genre->getDescription();

            if ($genreName === '') {
                $genreName = 'Unbekanntes Genre';
            }

            $imageFileName = $genre->getImagefilename();

            $imageUrl = $imageFileName
                    ? genreImageUrl($imageFileName, 'square-small')
                    : base_url('images/genres/square-medium/');
            ?>

            <article class="genre-card-link-wrapper">
                <a class="genre-card-link" href="<?= e(genreDetailUrl($genreId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($genreName); ?>"
                            class="genre-card-image"
                    >

                    <div class="genre-card-content">
                        <h2><?= e($genreName); ?></h2>
                        <p>Era: <?= e($era); ?></p>
                        <p><?= e($description); ?></p>
                        <span class="text-link">Einzelansicht öffnen</span>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>