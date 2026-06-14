<?php
/**
 * Review-Löschaktion für UC17.
 *
 * Erlaubt nur Administratoren das Löschen einer Bewertung und prüft zusätzlich,
 * ob die übergebene Review-ID zum übergebenen Kunstwerk gehört.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';

if (!isAdmin())
{
    header('Location: ' . base_url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ' . base_url('index.php'));
    exit;
}

$reviewId = filter_var($_POST['review_id'] ?? null, FILTER_VALIDATE_INT);
$artworkId = filter_var($_POST['artwork_id'] ?? null, FILTER_VALIDATE_INT);

if ($reviewId === false || $reviewId <= 0 || $artworkId === false || $artworkId <= 0)
{
    header('Location: ' . base_url('index.php'));
    exit;
}

$db = new dbaccess();
$db->connect();

// Loads the review first so the artwork relation can be verified before deletion.
$reviewRepository = new reviewRepository($db);
$review = $reviewRepository->getById((int) $reviewId);

if (!$review)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
    exit;
}

// Prevents deleting a review through a mismatched artwork ID.
if ((int) $review->getArtworkId() !== (int) $artworkId)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
    exit;
}

$deleted = $reviewRepository->deleteReview((int) $reviewId);

if ($deleted)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=deleted#reviews');
    exit;
}

header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
exit;