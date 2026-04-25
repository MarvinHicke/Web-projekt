<?php

require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$db = new dbaccess();
$db->connect();
$pdo = $db->pdo;

if (!isset($pdo))
{
    die("Fehler: Die Variable \$pdo wurde nicht erstellt. Prüfe: dbaccess.php");
}




$artistRepo = new artistRepository($pdo);

$allArtists = $artistRepo->findAll();

foreach ($allArtists as $artist)
{
    echo "<li>" . $artist->getFirstName() . " " . $artist->getLastName() . "</li>";
}

$genreRepo = new genreRepository($pdo);

$allGenre = $genreRepo->findAll();

foreach ($allGenre as $genres)
{
    echo "<li>" . $genres->getGenreName() . " " . $genres->getDescription() . "</li>";
}

$artworkRepo = new artworkRepository($pdo);

$allArtworks = $artworkRepo->findAll();

foreach ($allArtworks as $artworks)
{
    echo "<li>" . $artworks->getTitle() . " " . $artworks->getDescription() . "</li>";
}



$db->close();


?>


