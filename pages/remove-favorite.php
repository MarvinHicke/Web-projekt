<?php
require_once 'includes/init.php';
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
$_SESSION["favorites"][$favoriteKey]=array_values(
    array_filter(
        $_SESSION["favorites"][$favoriteKey],
        static function ($favoriteId) use ($id) {
            return (int)$favoriteId !== $id;
        }));

header("Location: favorites.php");
exit;