<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Suchergebnisse';
$query = trim((string) ($_GET['q'] ?? ''));

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Suchergebnisse</h1>

<?php if ($query === ''): ?>
    <p>Bitte gib einen Suchbegriff ein.</p>
<?php else: ?>
    <p>Suchergebnisse für: <strong><?= e($query) ?></strong></p>
    <p>Die echte Suche wird später mit den Datenbankdaten verbunden.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>