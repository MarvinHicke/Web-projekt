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

$db->close();
