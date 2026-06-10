<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/galleryRepository.php';

$db = new dbaccess();
$db->connect();
$galleryRepo = new galleryRepository($db);

echo "<h1>Repository Test: Gallery</h1>";

$all = $galleryRepo->findAll();

echo "<p>Methode: findAll()</p>";
if (is_array($all) && count($all) > 0)
{
    echo "Ergebnis (Anzahl): " . count($all) . " Galerien gefunden - erfolgreich<br>";
    $firstGallery = $all[0];
    $id = $firstGallery->getGalleryID();
} else
{
    echo "Ergebnis: Keine Galerien gefunden - FEHLGESCHLAGEN<br>";
    $id = 1;
}

echo "<p>Methode: getById()</p>";
$gallery = $galleryRepo->getById($id);

if ($gallery && $gallery->getGalleryID() === $id)
{
    echo "Ergebnis: Galerie mit ID " . $id . " (" . $gallery->getGalleryName() . ") erfolgreich geladen<br>";
} else
{
    echo "Ergebnis: Galerie mit ID " . $id . " konnte nicht geladen werden - FEHLGESCHLAGEN<br>";
}

$db->close();