<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Künstler durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$artistRepository = new artistRepository(db());
$artists = $artistRepository->findAll();

require_once __DIR__.'/../includes/header.php';

?>

<section class="page-heading">
    <h1>Künstler durchsuchen</h1>
    <p>Entdecken Sie Künstlerinnen und Künstler.</p>
</section>

<section class="container my-4">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($artists as $artist): ?>
            <?php include __DIR__ . '/../components/artist-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
