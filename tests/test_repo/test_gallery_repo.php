<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/galleryRepository.php';

$db = new dbaccess();
$db->connect();
$galleryRepo = new galleryRepository($db);

echo "<h1>Repository Test: Gallery</h1>";

// FindAll-Funktionstest
$all = $galleryRepo->findAll();

echo "<p>Methode: findAll()</p>";
echo "Ergebnis (Anzahl): " . count($all) . " Bilder gefunden";

$db->close();

