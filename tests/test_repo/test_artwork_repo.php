<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/artworkRepository.php';


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

echo "<h2>Methode: getTopArtworks(3)</h2>";

$topWorks = $artworkRepo->getTopArtworks(3);

if (!empty($topWorks))
{
    foreach ($topWorks as $work)
    {
        echo "Kunstwerk: " . $work['Title'] . " - Reviews: " . $work['AvgRating'] . "<br>";
    }
}
else
{
    echo "Ergebnis: Keine Kunstwerke oder Reviews gefunden.";
}

echo "<h2>Methode: getAllSorted - Mit Limiter</h2>";

$sortArtwork = $artworkRepo->getAllSorted('title', 'ASC');

if (!empty($sortArtwork))
{
    $i = 0;
    foreach ($sortArtwork as $sort)
    {
        if ($i >= 5) break;
        echo $sort->getTitle() . "<br>";
        $i++;
    }
}
else
{
    echo "Ergebnis: Keine Kunstwerke gefunden.<br>";
}

echo "<h2>Methode: getForArtist(1)</h2>";

$artistWorks = $artworkRepo->getForArtist(1);
if (!empty($artistWorks))
{
    echo "Ergebnis: " . count($artistWorks) . " Kunstwerke gefunden.<br>";
    echo "Beispiel: " . $artistWorks[0]->getTitle() . "<br>";
}
else
{
    echo "Ergebnis: Keine Kunstwerke gefunden.<br>";
}

echo "<h2>Methode: getForGenre(1)</h2>";

$genreWorks = $artworkRepo->getForGenre(1);
if (!empty($genreWorks))
{
    echo "Ergebnis: " . count($genreWorks) . " Kunstwerke gefunden.<br>";
}
else
{
    echo "Ergebnis: Keine Kunstwerke gefunden.<br>";
}

echo "<h2>Methode: getForSubject(1)</h2>";

$subjectWorks = $artworkRepo->getForSubject(1);
if (!empty($subjectWorks))
{
    echo "Ergebnis: " . count($subjectWorks) . " Kunstwerke gefunden.<br>";
}
else
{
    echo "Ergebnis: Keine Kunstwerke gefunden.<br>";
}

echo "<h2>Methode: searchByTitle('Mona')</h2>";

$searchWorks = $artworkRepo->searchByTitle('Mona');
if (!empty($searchWorks))
{
    foreach ($searchWorks as $work)
    {
        echo "Treffer: " . $work->getTitle() . "<br>";
    }
}
else
{
    echo "Ergebnis: Keine Treffer gefunden.<br>";
}

$db->close();
