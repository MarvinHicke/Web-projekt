<?php


require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/subjectRepository.php';

$db = new dbaccess();
$db->connect();
$subjectRepo = new subjectRepository($db);

echo "<h1>Repository Test: Subject</h1>";

// FindAll-Funktionstest
$all = $subjectRepo->findAll();

echo "<p>Methode: findAll()</p>";
echo "Ergebnis (Anzahl): " . count($all) . " Subjects gefunden";

$db->close();

