<?php
$pageTitle = 'Home · Art Gallery';

require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$db = new dbaccess();
$db->connect();
$pdo = $db->pdo;

if (!isset($pdo)) {
    die("Fehler: Die Variable \$pdo wurde nicht erstellt. Prüfe: dbaccess.php");
}

$artistRepo = new artistRepository($pdo);
$genreRepo = new genreRepository($pdo);
$artworkRepo = new artworkRepository($pdo);

$artists = $artistRepo->findAll();
$genres = $genreRepo->findAll();
$artworks = $artworkRepo->findAll();
?>

<section class="hero">
    <div>
        <p class="eyebrow">Welcome to</p>
        <h1>Art Gallery</h1>
        <p>Discover artworks, artists, reviews and galleries.</p>
        <a class="button-link" href="browse-artworks.php">Browse Artworks</a>
    </div>
</section>

<section class="carousel-placeholder" aria-label="Featured artworks carousel placeholder">
    <h2>Featured artworks carousel</h2>
    <p>This area is planned as a carousel. In Sprint 1 it is only a layout placeholder.</p>

    <div class="carousel-strip">
        <?php foreach (array_slice($artworks, 0, 3) as $work): ?>
            <article class="carousel-card">
                <h3><?= e((string) $work->getTitle()); ?></h3>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="three-box-grid" aria-label="Homepage data boxes">
    <div class="info-box">
        <h2>Artists</h2>
        <ul>
            <?php foreach (array_slice($artists, 0, 5) as $artist): ?>
                <li><?= e($artist->getFirstName() . ' ' . $artist->getLastName()); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="info-box">
        <h2>Genres</h2>
        <ul>
            <?php foreach (array_slice($genres, 0, 5) as $genre): ?>
                <li><?= e($genre->getGenreName()); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="info-box">
        <h2>Artworks</h2>
        <ul>
            <?php foreach (array_slice($artworks, 0, 5) as $artwork): ?>
                <li><?= e($artwork->getTitle()); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php
$db->close();
require_once __DIR__ . '/../includes/footer.php';
?>