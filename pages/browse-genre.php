<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Browse Genre';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$genreRepository = new genreRepository(db());
$genres = $genreRepository->findAll();

require_once __DIR__.'/../includes/header.php';

?>

<section class="page-heading">
    <h1>Genre durchsuchen</h1>
    <p>Entdecken sie Genres</p>
</section>

<section class="container my-4">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($genres as $genre): ?>
            <?php include __DIR__ . '/../components/artist-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
