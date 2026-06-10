<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/genreRepository.php';

$db = new dbaccess();
$db->connect();
$genreRepo = new genreRepository($db);

echo "<h1>Repository Test: Genre</h1>";

$all = $genreRepo->findAll();

echo "<p>Methode: findAll()</p>";
if (is_array($all) && count($all) > 0)
{
    echo "Ergebnis (Anzahl): " . count($all) . " Genres gefunden - erfolgreich<br>";
    $firstGenre = $all[0];
    $id = $firstGenre->getGenreID();
} else
{
    echo "Ergebnis: Keine Genres gefunden - FEHLGESCHLAGEN<br>";
    $id = 1;
}

echo "<p>Methode: getById()</p>";
$genre = $genreRepo->getById($id);
if ($genre && $genre->getGenreID() === $id)
{
    echo "Ergebnis: Genre mit ID " . $id . " (" . $genre->getGenreName() . ") erfolgreich geladen<br>";
} else
{
    echo "Ergebnis: Genre mit ID " . $id . " konnte nicht geladen werden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getAllForBrowse()</p>";
$browse = $genreRepo->getAllForBrowse();
if (is_array($browse) && count($browse) > 0)
{
    echo "Ergebnis (Anzahl): " . count($browse) . " Genres sortiert gefunden - erfolgreich<br>";
} else
{
    echo "Ergebnis: Keine Genres gefunden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getGenresForArtwork()</p>";
$artworkId = 1;
$artworkGenres = $genreRepo->getGenresForArtwork($artworkId);
if (is_array($artworkGenres))
{
    echo "Ergebnis (Anzahl): " . count($artworkGenres) . " Genres für Artwork ID " . $artworkId . " gefunden - erfolgreich<br>";
} else
{
    echo "Ergebnis: Fehler bei der Abfrage - FEHLGESCHLAGEN<br>";
}

$db->close();