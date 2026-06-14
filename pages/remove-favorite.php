<?php
/**
 * Entfernt ein Kunstwerk oder einen Künstler aus den sessionbasierten Favoriten.
 *
 * Erlaubte Weiterleitungsziele werden begrenzt, damit keine offenen Redirects
 * über beliebige URLs entstehen.
 */
require_once __DIR__ . '/../includes/init.php';

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if (!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}

if (!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}

if (!isset($_SESSION["favorites"]["artists"]))
{
    $_SESSION["favorites"]["artists"] = [];
}

$type = (string) ($_GET["type"] ?? "");
$id = (int) ($_GET["id"] ?? 0);
$redirectTarget = (string) ($_GET["redirect"] ?? "favorites.php");

$allowedTypes = ["artwork", "artist"];

// Restricts redirects to known project pages after changing session favorites.
$allowedRedirects = ["favorites.php", "browse-artists.php", "browse-artworks.php", "single-artist.php", "single-artwork.php"];

if (!in_array($redirectTarget, $allowedRedirects, true))
{
    $redirectTarget = "favorites.php";
}

if (!in_array($type, $allowedTypes, true))
{
    header("Location: favorites.php");
    exit;
}

if ($id <= 0)
{
    header("Location: favorites.php");
    exit;
}

if ($redirectTarget === "single-artwork.php" && $type === "artwork")
{
    $redirectTarget = "single-artwork.php?id=" . urlencode((string) $id);
}

if ($redirectTarget === "single-artist.php" && $type === "artist")
{
    $redirectTarget = "single-artist.php?id=" . urlencode((string) $id);
}

$favoriteKey = $type === "artwork" ? "artworks" : "artists";

$_SESSION["favorites"][$favoriteKey] = array_values(
    array_filter(
        $_SESSION["favorites"][$favoriteKey],
        static function ($favoriteId) use ($id)
        {
            return (int) $favoriteId !== $id;
        }
    )
);

header("Location: " . $redirectTarget);
exit;