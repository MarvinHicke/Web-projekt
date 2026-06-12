<?php

require_once __DIR__ . '/../includes/init.php';

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if(!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}
if(!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}
if(!isset($_SESSION["favorites"]["artists"]))
{
    $_SESSION["favorites"]["artists"] = [];
}

$type=(string) ($_GET["type"] ?? "");
$id=(int) ($_GET["id"] ?? 0);
$allowedTypes=["artwork","artist"];
$redirectTarget = (string) ($_GET["redirect"] ?? "favorites.php");

$allowedRedirects = ["favorites.php", "browse-artists.php", "browse-artworks.php", "single-artist.php", "single-artwork.php"];

if (!in_array($redirectTarget, $allowedRedirects, true)) {
    $redirectTarget = "favorites.php";
}
if(!in_array($type, $allowedTypes, true))
{
    header("Location: " . $redirectTarget);
    exit;
}
if($id<=0)
{
    header("Location: " . $redirectTarget);
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

$favoriteKey=$type==="artwork"?"artworks":"artists";

if (!in_array($id, $_SESSION["favorites"][$favoriteKey], true))
{
    $_SESSION["favorites"][$favoriteKey][] = $id;
}

header("Location: " . $redirectTarget);
exit;
?>
