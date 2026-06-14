<?php
/**
 * Entfernt ein Kunstwerk oder einen Künstler aus den sessionbasierten Favoriten.
 *
 * Erlaubte Weiterleitungsziele werden begrenzt, damit keine offenen Redirects
 * über beliebige URLs entstehen.
 */
require_once __DIR__ . '/../includes/init.php';

// Session starten, falls sie noch nicht aktiv ist.
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

// Grundstruktur für sessionbasierte Favoriten sicherstellen.
if (!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}

// Favoritenliste für Kunstwerke initialisieren, falls sie noch nicht existiert.
if (!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}

// Favoritenliste für Künstler initialisieren, falls sie noch nicht existiert.
if (!isset($_SESSION["favorites"]["artists"]))
{
    $_SESSION["favorites"]["artists"] = [];
}

// Typ, ID und gewünschtes Weiterleitungsziel aus der URL lesen.
$type = (string) ($_GET["type"] ?? "");
$id = (int) ($_GET["id"] ?? 0);
$redirectTarget = (string) ($_GET["redirect"] ?? "favorites.php");

// Erlaubte Favoritentypen festlegen.
$allowedTypes = ["artwork", "artist"];

// Weiterleitungen auf bekannte Projektseiten beschränken, nachdem Session-Favoriten geändert wurden.
$allowedRedirects = ["favorites.php", "browse-artists.php", "browse-artworks.php", "single-artist.php", "single-artwork.php"];

// Ungültige Weiterleitungsziele auf die Favoritenseite zurücksetzen.
if (!in_array($redirectTarget, $allowedRedirects, true))
{
    $redirectTarget = "favorites.php";
}

// Ungültige Favoritentypen abbrechen und zur Favoritenseite zurückleiten.
if (!in_array($type, $allowedTypes, true))
{
    header("Location: favorites.php");
    exit;
}

// Ungültige IDs abbrechen und zur Favoritenseite zurückleiten.
if ($id <= 0)
{
    header("Location: favorites.php");
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
$favoriteKey = $type === "artwork" ? "artworks" : "artists";

// Ausgewählte ID aus der passenden Favoritenliste entfernen und die Array-Indizes neu aufbauen.
$_SESSION["favorites"][$favoriteKey] = array_values(
    array_filter(
        $_SESSION["favorites"][$favoriteKey],
        static function ($favoriteId) use ($id)
        {
            return (int) $favoriteId !== $id;
        }
    )
);

// Nach dem Entfernen zur passenden Seite zurückleiten.
header("Location: " . $redirectTarget);
exit;