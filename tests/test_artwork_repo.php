<?php

require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$db = new dbaccess();
$db->connect();
$artworkRepo = new artworkRepository($db);

echo "<h1>Repository Test: Artwork</h1>";

// FindAll-Funktionstest
$all = $artworkRepo->findAll();

echo "<p>Methode: findAll()</p>";
echo "Ergebnis (Anzahl): " . count($all) . " Artworks gefunden";

// GetById-Funktionstest
$testId = 6; // <-- Gesuchte ID
$getartworkbyid = $artworkRepo->getById($testId);
echo "<h2>Methode: getById($testId)</h2>";
echo "<p>Gesuchte ID = $testId</p>";

if ($getartworkbyid) {
    echo "<p>Ergebnis: " . $getartworkbyid->getTitle() . "</p>";
    echo "<p>Beschreibung: " . $getartworkbyid->getDescription() . "</p>";
}
else
{
    echo "Ergebnis: Nicht gefunden";
}

$db->close();
