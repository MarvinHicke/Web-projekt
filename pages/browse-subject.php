<?php
// Enable detailed error output during development.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set the page title before loading the shared header.
$pageTitle = 'Subject durchsuchen';

// Load application initialization, shared helpers, and the subject repository.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

try {
    // Open the database connection and load all subjects.
    $db = new dbaccess();
    $db->connect();

    $subjectRepository = new subjectRepository($db);
    $subjects = $subjectRepository->findAll();
} catch (Throwable $e) {
    // Stop execution and show the error message during development.
    die($e->getMessage());
}

// Render the shared header after all page data has been prepared.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Page heading for the subject browse page. -->
    <section class="page-heading">
        <h1>Subjects durchsuchen</h1>
        <p>Entdecken Sie Subjects.</p>
    </section>

<?php if (empty($subjects)): ?>
    <!-- Empty-state message shown when no subjects are available. -->
    <section class="message">
        Es wurde kein Subject gefunden.
    </section>
<?php else: ?>
    <!-- Responsive grid containing all subject cards. -->
    <section class="subject-card-grid" aria-label="Liste der Subjects">
        <?php foreach ($subjects as $subject): ?>
            <?php
            // Prepare display values for the current subject card.
            $subjectId = $subject->getSubjectId();
            $subjectName = $subject->getSubjectName();

            // Use a fallback label if the subject name is missing.
            if ($subjectName === '') {
                $subjectName = 'Unbekanntes Subject';
            }

            // Build the subject image URL or use a placeholder image as fallback.
            $imageFileName = $subject->getImagefilename();

            $imageUrl = $imageFileName
                    ? subjectImageUrl($imageFileName, 'square-medium')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Single subject card. -->
            <article class="subject-card-link-wrapper">
                <a href="<?= e(subjectDetailUrl($subjectId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($subjectName); ?>"
                            class="subject-card-image"
                    >
                </a>

                <div class="subject-card-content">
                    <h2>
                        <a href="<?= e(subjectDetailUrl($subjectId)); ?>">
                            <?= e($subjectName); ?>
                        </a>
                    </h2>

                    <div class="result-actions">
                        <a class="btn btn-sm btn-primary" href="<?= e(subjectDetailUrl($subjectId)); ?>">
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