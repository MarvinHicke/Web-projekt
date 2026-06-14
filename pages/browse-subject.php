<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Subject durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

$sort = safeParam((string) ($_GET['sort'] ?? 'subjectName'), ['subjectName'], 'subjectName');

try {
    $db = new dbaccess();
    $db->connect();
    $subjectRepository = new subjectRepository($db);
    $subjects = $subjectRepository->findAll();
} catch (Throwable $e) {
    die($e->getMessage());
}

$_SESSION["favorites"] ??= [];
$_SESSION["favorites"]["subjects"] ??= [];

$favoriteSubjectsIds = array_map('intval', $_SESSION["favorites"]["subjects"]);

require_once __DIR__.'/../includes/header.php';

?>
    <section class="page-heading">
        <h1>Subjects durchsuchen</h1>
        <p>Entdecken sie Subjects</p>
    </section>


<?php if (empty($subjects)): ?>
    <section class="message">
        Es wurde kein Subject gefunden.
    </section>
<?php else: ?>
    <section class="subject-card-grid" aria-label="Liste der Subjects">
        <?php foreach ($subjects as $subject): ?>
            <?php
            $subjectId = $subject->getSubjectId();

            $subjectName = $subject->getSubjectName();

            if ($subjectName === '') {
                $subjectName = 'Unbekanntes Subject';
            }

            $imageFileName = $subject->getImagefilename();
            $imageFileName = $subject->getImagefilename();

            $imageUrl = $imageFileName
                    ? subjectImageUrl($imageFileName, 'square-medium')
                    : base_url('images/placeholder.jpg');
            ?>

            <article class="subject-card-link-wrapper">
                <a class="subject-card-link" href="<?= e(subjectDetailUrl($subjectId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($subjectName); ?>"
                            class="subject-card-image"
                    >

                    <div class="subject-card-content">
                        <h2><a href="<?= e(subjectDetailUrl($subjectId)); ?>"><?= e($subjectName); ?></a></h2>
                        <div class="result-actions">
                            <a class="btn btn-sm btn-primary" href="<?= e(subjectDetailUrl($subjectId)); ?>">Ansehen</a>

                        </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>