<?php
// Alle Fehleranzeigen werden ausgegeben mit E_ALL
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/artworkRepository.php';

$db = new dbaccess();
$db->connect();

if (!isset($db))
{
    die("Fehler: Datenbankverbindung konnte nicht geladen werden.");
}

$artworkRepo = new artworkRepository($db);

// Der Standardwert ist auf 1 gesetzt. Kann auch auf null umgeändert werden.
$id = isset($_GET['id']) ? $_GET['id'] : 1;

$artwork = $artworkRepo->getById($id);

if ($artwork === null)
{
    die("<h1>Fehler</h1><p>Artwork mit ID $id nicht gefunden.</p>");
}

$db->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Details: <?php echo $artwork->getTitle(); ?></title>
</head>
<body>
<h1>Künstlerprofil</h1>
<ul>
    <li><strong>Titel:</strong> <?php echo $artwork->getTitle(); ?></li>
    <li><strong>Beschreibung:</strong> <?php echo $artwork->getDescription(); ?></li>
    <li><strong>ID:</strong> <?php echo $id; ?></li>
</ul>

<p><a href="/web-projekt/index.php">Zurück zur Startseite</a></p>
</body>
</html>

