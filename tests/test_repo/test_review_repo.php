<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/reviewRepository.php';

$db = new dbaccess();
$db->connect();
$repo = new reviewRepository($db);

echo "<h1>Repository Test: Review</h1>";

$customerStmt = $db->preparedStatement("SELECT CustomerID FROM customerlogon LIMIT 1");
$customerStmt->execute();
$customerRow = $customerStmt->fetch(PDO::FETCH_ASSOC);

$customerId = $customerRow ? $customerRow['CustomerID'] : 1;
$artworkId = 1;
$rating = 5;
$comment = 'Test Kommentar ' . time();

echo "<p>Methode: addReview()</p>";
if ($repo->addReview($artworkId, $customerId, $rating, $comment))
{
    echo "Ergebnis: Bewertung erfolgreich hinzugefügt - erfolgreich<br>";
} else
{
    echo "Ergebnis: Hinzufügen fehlgeschlagen - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getLatestReviews()</p>";
$latest = $repo->getLatestReviews(1);
if (is_array($latest) && count($latest) > 0)
{
    echo "Ergebnis: Neueste Bewertung geladen (" . $latest[0]->getComment() . ") - erfolgreich<br>";
    $reviewId = $latest[0]->getReviewId();
} else
{
    echo "Ergebnis: Keine Reviews gefunden - FEHLGESCHLAGEN<br>";
    $reviewId = 1;
}

echo "<p>Methode: hasUserReviewedArtwork()</p>";
if ($repo->hasUserReviewedArtwork($artworkId, $customerId))
{
    echo "Ergebnis: Prüfung ob User bewertet hat positiv - erfolgreich<br>";
} else
{
    echo "Ergebnis: Prüfung negativ - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getById()</p>";
$review = $repo->getById($reviewId);
if ($review && $review->getReviewId() === $reviewId)
{
    echo "Ergebnis: Review mit ID " . $reviewId . " erfolgreich geladen - erfolgreich<br>";
} else
{
    echo "Ergebnis: Review konnte nicht geladen werden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getForArtwork()</p>";
$artworkReviews = $repo->getForArtwork($artworkId);
if (is_array($artworkReviews) && count($artworkReviews) > 0)
{
    echo "Ergebnis (Anzahl): " . count($artworkReviews) . " Reviews für Artwork gefunden - erfolgreich<br>";
} else
{
    echo "Ergebnis: Keine Reviews für Artwork gefunden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getAverageRatingArtwork()</p>";
$ratingData = $repo->getAverageRatingArtwork($artworkId);
if ($ratingData)
{
    $average = $ratingData['AvgRating'] !== null ? round($ratingData['AvgRating'], 2) : "Keine Bewertungen";
    $count = $ratingData['TotalReviews'];
    echo "Ergebnis: Durchschnitt " . $average . " bei " . $count . " Bewertungen - erfolgreich<br>";
} else
{
    echo "Ergebnis: Fehler bei Datenabruf - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: deleteReview()</p>";
if ($repo->deleteReview($reviewId))
{
    echo "Ergebnis: Test-Bewertung mit ID " . $reviewId . " erfolgreich gelöscht - erfolgreich<br>";
} else
{
    echo "Ergebnis: Löschen fehlgeschlagen - FEHLGESCHLAGEN<br>";
}

$db->close();