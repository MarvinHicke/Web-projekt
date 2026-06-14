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

echo "<h2>Methode: getMostReviewedArtists(3)</h2>";

$topArtists = $artistRepo->getMostReviewedArtists(3);

if (!empty($topArtists))
{
    foreach ($topArtists as $artist)
    {
        echo "Künstler: " . $artist['FirstName'] . " " . $artist['LastName'] . " ". "-  Reviews: " . $artist['ReviewCount'] . "<br>";
    }
} else
{
    echo "Ergebnis: Keine Künstler oder Reviews gefunden.";
}

echo "<h2>Methode: getAllSorted - Mit Limiter</h2>";

$sortArtist = $artistRepo->getAllSorted('ASC');

if (!empty($sortArtist))
{
    $i = 0;
    foreach ($sortArtist as $sort)
    {
        if ($i >= 5)
        {
            break;
        }

        echo $sort->getFirstName() . " " . $sort->getLastName() . "<br>";
        $i++;
    }
}
else
{
    echo "Ergebnis: Keine Kunstwerke gefunden.<br>";
}

echo "<h2>Methode: searchByLastName('Pic')</h2>";

$searchedArtists = $artistRepo->searchByLastName('Pic');

if (!empty($searchedArtists))
{
    foreach ($searchedArtists as $artist)
    {
        echo "Treffer: " . $artist->getFirstName() . " " . $artist->getLastName() . "<br>";
    }
}
else
{
    echo "Ergebnis: Keine passenden Künstler gefunden.<br>";
}

echo "<h2>Methode: artistRepository - Methode: AdvancedSearch('Picasso')</h2>";
$artists = $artistRepo->advancedSearch('Picasso');

if (!empty($artists))
{
    echo "Ergebnis: " . $artists[0]->getFirstName() . " " . $artists[0]->getLastName() . " gefunden<br>";
} else
{
    echo "Ergebnis: Keine passenden Künstler gefunden<br>";
}

$db->close();


