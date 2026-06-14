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

// Zugriffsschutz: Nur Administratoren dürfen Bewertungen löschen.
if (!isAdmin())
{
    header('Location: ' . base_url('index.php'));
    exit;
}

// Nur POST-Anfragen erlauben, damit Bewertungen nicht über einfache Links gelöscht werden.
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ' . base_url('index.php'));
    exit;
}

// Review-ID und Kunstwerk-ID aus dem Formular lesen und als Integer validieren.
$reviewId = filter_var($_POST['review_id'] ?? null, FILTER_VALIDATE_INT);
$artworkId = filter_var($_POST['artwork_id'] ?? null, FILTER_VALIDATE_INT);

// Ungültige oder fehlende IDs führen zurück zur Startseite.
if ($reviewId === false || $reviewId <= 0 || $artworkId === false || $artworkId <= 0)
{
    header('Location: ' . base_url('index.php'));
    exit;
}

// Datenbankverbindung öffnen.
$db = new dbaccess();
$db->connect();

// Review zuerst laden, damit die Zuordnung zum Kunstwerk vor dem Löschen geprüft werden kann.
$reviewRepository = new reviewRepository($db);
$review = $reviewRepository->getById((int) $reviewId);

// Bei nicht gefundener Bewertung zurück zur Kunstwerkseite mit Fehlermeldung.
if (!$review)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
    exit;
}

// Verhindert das Löschen einer Bewertung über eine nicht passende Kunstwerk-ID.
if ((int) $review->getArtworkId() !== (int) $artworkId)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
    exit;
}

// Bewertung löschen.
$deleted = $reviewRepository->deleteReview((int) $reviewId);

// Bei erfolgreichem Löschen zurück zur Kunstwerkseite mit Erfolgsmeldung.
if ($deleted)
{
    header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=deleted#reviews');
    exit;
}

// Fallback: Bei fehlgeschlagenem Löschen zurück zur Kunstwerkseite mit Fehlermeldung.
header('Location: ' . base_url('pages/single-artwork.php') . '?id=' . (int) $artworkId . '&review=delete-error#reviews');
exit;