<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Themen durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

$subjectRepository = new subjectRepository(db());
$subjects = $subjectRepository->findAll();

require_once __DIR__.'/../includes/header.php';

?>

<section class="page-heading">
    <h1>Themen durchsuchen</h1>
    <p>Entdecken Sie die Themen der Kunstwerke.</p>
</section>

<section class="container my-4">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($subjects as $subject): ?>
            <?php include __DIR__ . '/../components/subject-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
