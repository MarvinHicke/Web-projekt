<?php
require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/reviewRepository.php';

$db = new dbaccess();
$db->connect();
$repo = new reviewRepository($db);

echo "<h2>Test: Neueste Reviews</h2>";
$latest = $repo->getLatestReviews(3);

foreach ($latest as $review)
{
    echo "Rating: " . $review->rating . " - Kommentar: " . $review->comment;
}

echo "<br><br>";

$artworkId = 8;
$ratingData = $repo->getAverageRatingArtwork($artworkId);

if ($ratingData)
{
    $average = $ratingData['AvgRating'] !== null ? round($ratingData['AvgRating'], 2) : "Keine Bewertungen";
    $count = $ratingData['TotalReviews'];

    echo "Kunstwerk-ID $artworkId <br>";
    echo "Durchschnittliche Bewertung: " . $average . "<br>";
    echo "Anzahl der Gesamtbewertunugen:" . $count . "<br>";
}
else
{
    echo "Fehler bei Datenabruf<br>";
}

$db->close();


