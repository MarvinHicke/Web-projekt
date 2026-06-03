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
if(!in_array($type, $allowedTypes, true))
{
    header("Location: favorites.php");
    exit;
}
if($id<=0)
{
    header("Location: favorites.php");
    exit;
}
$favoriteKey=$type==="artwork"?"artworks":"artists";
if (!in_array($id, $_SESSION["favorites"][$favoriteKey], true))
{
    $_SESSION["favorites"][$favoriteKey][] = $id;
}

header("Location: favorites.php");
exit;
?>
