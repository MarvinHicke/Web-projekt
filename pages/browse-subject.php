<?php
// Detaillierte Fehlerausgabe während der Entwicklung aktivieren.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Seitentitel vor dem Laden des Headers setzen.
$pageTitle = 'Subject durchsuchen';

// Initialisierung, gemeinsame Hilfsfunktionen und Repository laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

try {
    // Datenbankverbindung öffnen und Subjects laden.
    $db = new dbaccess();
    $db->connect();

    $subjectRepository = new subjectRepository($db);
    $subjects = $subjectRepository->findAll();
} catch (Throwable $e) {
    // Fehler während der Entwicklung direkt ausgeben.
    die($e->getMessage());
}

// Gemeinsamen Header einbinden, nachdem die Seitendaten vorbereitet wurden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Seitenüberschrift für die Subjectübersicht. -->
    <section class="page-heading">
        <h1>Themen durchsuchen</h1>
        <p>Entdecken Sie die Themen zu den Kunstwerken.</p>
    </section>

<?php if (empty($subjects)): ?>
    <!-- Hinweis, falls keine Subjects vorhanden sind. -->
    <section class="message">
        Es wurde kein Thema gefunden.
    </section>
<?php else: ?>
    <!-- Kartenraster mit allen Subjects. -->
    <section class="subject-card-grid" aria-label="Liste der Subjects">
        <?php foreach ($subjects as $subject): ?>
            <?php
            // Anzeigewerte für die aktuelle Subjectkarte vorbereiten.
            $subjectId = $subject->getSubjectId();
            $subjectName = $subject->getSubjectName();

            // Fallback verwenden, falls kein Subjectname vorhanden ist.
            if ($subjectName === '') {
                $subjectName = 'Unbekanntes Subject';
            }

            // Subjectbild laden oder Platzhalter verwenden.
            $imageFileName = $subject->getImagefilename();

            $imageUrl = $imageFileName
                    ? subjectImageUrl($imageFileName, 'square-medium')
                    : base_url('images/placeholder.jpg');
            ?>

            <!-- Einzelne Subjectkarte. -->
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
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>