<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Genre durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$sort = safeParam((string) ($_GET['sort'] ?? 'era'), ['era', 'genreName'], 'era');

try {
    $db = new dbaccess();
    $db->connect();
    $genreRepository = new genreRepository($db);
    $genres = $genreRepository->findAll();
} catch (Throwable $e) {
    die($e->getMessage());
}

require_once __DIR__.'/../includes/header.php';

?>
    <section class="page-heading">
        <h1>Genres durchsuchen</h1>
        <p>Entdecken Sie Kunstrichtungen und Epochen.</p>
    </section>


<?php if (empty($genres)): ?>
    <section class="message">
        Es wurden keine Genres gefunden.
    </section>
<?php else: ?>
    <section class="container my-4" aria-label="Liste der Genres">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($genres as $genre): ?>
                <?php include __DIR__ . '/../components/genre-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
