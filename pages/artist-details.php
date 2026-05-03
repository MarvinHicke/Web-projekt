<?php
// Alle Fehleranzeigen werden ausgegeben mit E_ALL
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$db = new dbaccess();
$db->connect();

if (!isset($db)) {
    die("Fehler: Datenbankverbindung konnte nicht geladen werden.");
}

$artistRepo = new artistRepository($db);

// Der Standardwert ist auf 1 gesetzt. Kann auch auf null umgeändert werden.
$id = isset($_GET['id']) ? $_GET['id'] : 1;

$artist = $artistRepo->getById($id);

if ($artist === null) {
    die("<h1>Fehler</h1><p>Künstler mit ID $id nicht gefunden.</p>");
}

$db->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Details: <?php echo $artist->getFirstName(); ?></title>
</head>
<body>
<h1>Künstlerprofil</h1>
<ul>
    <li><strong>Vorname:</strong> <?php echo $artist->getFirstName(); ?></li>
    <li><strong>Nachname:</strong> <?php echo $artist->getLastName(); ?></li>
    <li><strong>ID:</strong> <?php echo $id; ?></li>
</ul>

<p><a href="index.php">Zurück zur Liste</a></p>
</body>
</html>

