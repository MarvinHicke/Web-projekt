
<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';
 
// Only admins, only POST
if (!isAdmin()) {
    header('Location: ' . base_url('index.php'));
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('index.php'));
    exit;
}
 
$reviewId  = (int) ($_POST['review_id']  ?? 0);
$artworkId = (int) ($_POST['artwork_id'] ?? 0);
 
if ($reviewId <= 0) {
    header('Location: ' . base_url('index.php'));
    exit;
}
 
try {
    $db = new dbaccess();
    $db->connect();
    $reviewRepo = new reviewRepository($db);
    $reviewRepo->deleteReview($reviewId);
} catch (Exception $e) {
    // silently fail, redirect back anyway
}
 
// Redirect back to the artwork page
$redirect = $artworkId > 0
    ? base_url('pages/single-artwork.php') . '?id=' . $artworkId
    : base_url('index.php');
 
header('Location: ' . $redirect);
exit;
