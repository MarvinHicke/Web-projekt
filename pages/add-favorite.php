<?php
/**
 * Fügt ein Kunstwerk oder einen Künstler zu den sessionbasierten Favoriten hinzu.
 *
 * Die Favoriten werden nur in der PHP-Session gespeichert und nicht in der
 * Datenbank persistiert.
 */
require_once __DIR__ . '/../includes/init.php';

// Session starten, falls sie noch nicht aktiv ist.
if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

// Grundstruktur für sessionbasierte Favoriten sicherstellen.
if(!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}

// Favoritenliste für Kunstwerke initialisieren, falls sie noch nicht existiert.
if(!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}

// Favoritenliste für Künstler initialisieren, falls sie noch nicht existiert.
if(!isset($_SESSION["favorites"]["artists"]))
{
    $_SESSION["favorites"]["artists"] = [];
}

// Typ, ID und gewünschtes Weiterleitungsziel aus der URL lesen.
$type=(string) ($_GET["type"] ?? "");
$id=(int) ($_GET["id"] ?? 0);
$allowedTypes=["artwork","artist"];
$redirectTarget = (string) ($_GET["redirect"] ?? "favorites.php");

// Weiterleitungen auf bekannte Projektseiten beschränken, nachdem Session-Favoriten geändert wurden.
$allowedRedirects = ["favorites.php", "browse-artists.php", "browse-artworks.php", "single-artist.php", "single-artwork.php"];

// Ungültige Weiterleitungsziele auf die Favoritenseite zurücksetzen.
if (!in_array($redirectTarget, $allowedRedirects, true)) {
    $redirectTarget = "favorites.php";
}

// Ungültige Favoritentypen abbrechen und zur Zielseite zurückleiten.
if(!in_array($type, $allowedTypes, true))
{
    header("Location: " . $redirectTarget);
    exit;
}

// Ungültige IDs abbrechen und zur Zielseite zurückleiten.
if($id<=0)
{
    header("Location: " . $redirectTarget);
    exit;
}

// Bei Rückleitung zur Kunstwerkdetailseite die ID wieder an die URL anhängen.
if ($redirectTarget === "single-artwork.php" && $type === "artwork")
{
    $redirectTarget = "single-artwork.php?id=" . urlencode((string) $id);
}

// Bei Rückleitung zur Künstlerdetailseite die ID wieder an die URL anhängen.
if ($redirectTarget === "single-artist.php" && $type === "artist")
{
    $redirectTarget = "single-artist.php?id=" . urlencode((string) $id);
}

// Passenden Session-Schlüssel abhängig vom Favoritentyp bestimmen.
$favoriteKey=$type==="artwork"?"artworks":"artists";

// ID nur hinzufügen, wenn sie noch nicht in der Favoritenliste vorhanden ist.
if (!in_array($id, $_SESSION["favorites"][$favoriteKey], true))
{
    $_SESSION["favorites"][$favoriteKey][] = $id;
}

// Nach dem Hinzufügen zur passenden Seite zurückleiten.
header("Location: " . $redirectTarget);
exit;
?>