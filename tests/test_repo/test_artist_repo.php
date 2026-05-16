<?php
require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/artistRepository.php';

$db = new dbaccess();
$db->connect();
$artistRepo = new artistRepository($db);

echo "<h1>Repository Test: Artist</h1>";

// FindAll-Funktionstest
$all = $artistRepo->findAll();

echo "<p>Methode: findAll()</p>";
echo "Ergebnis (Anzahl): " . count($all) . " Artists gefunden";

// GetById-Funktionstest
$testId = 1; // <-- Gesuchte ID
$getartistbyid = $artistRepo->getById($testId);
echo "<h2>Methode: getById($testId)</h2>";
echo "<p>Gesuchte ID = $testId</p>";

if ($getartistbyid)
{
    echo "Ergebnis: " . $getartistbyid->getFirstName() . " " . $getartistbyid->getLastName() . " gefunden";
}
else
{
    echo "Ergebnis: Nicht gefunden";
}

$db->close();
