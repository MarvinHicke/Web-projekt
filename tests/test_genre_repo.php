<?php

require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$db = new dbaccess();
$db->connect();
$genreRepo = new genreRepository($db);

echo "<h1>Repository Test: Genre</h1>";

// FindAll-Funktionstest
$all = $genreRepo->findAll();

echo "<p>Methode: findAll()</p>";
echo "Ergebnis (Anzahl): " . count($all) . " Genres gefunden";

$db->close();

