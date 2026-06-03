<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/init.php';

$pageTitle = 'Benutzer verwalten';

if (!isAdmin()) {
    header('Location: ' . base_url('index.php'));
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Benutzer verwalten</h1>
<p>Hier entsteht die Verwaltung der Benutzerkonten.</p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
