<?php

/** HTML-sichere, maskierte Ausgabe. */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Entfernt HTML-Tags aus Datenbankinhalten, z. B. <em> oder <p>, für reine Textausgaben. */
function cleanHtml(?string $text): string
{
    return trim(strip_tags((string) $text));
}

/** Prüft einen Parameter gegen eine Liste erlaubter Werte und gibt sonst einen Fallback zurück. */
function safeParam(string $value, array $allowed, string $fallback): string
{
    return in_array($value, $allowed, true) ? $value : $fallback;
}

/**
 * Gibt die URL zu einem Kunstwerkbild zurück und prüft mehrere Größenordner als Fallback.
 * Verhindert Platzhalterprobleme, wenn ein Bild nur in bestimmten Größen vorhanden ist.
 */
function artworkImageUrl(?string $imageFileName, string $size = 'square-small'): string
{
    $fileName = trim((string) $imageFileName);

    if ($fileName === '') {
        return base_url('images/placeholder.jpg');
    }

    if (!str_ends_with(strtolower($fileName), '.jpg')) {
        $fileName .= '.jpg';
    }

    // Zuerst die gewünschte Größe prüfen, danach die Fallback-Größen in Reihenfolge.
    $sizesToTry = array_unique([$size, 'square-small', 'small', 'medium', 'large', 'square-medium']);

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/works/' . $trySize . '/' . $fileName;
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

/** Gibt die URL zu einem Künstlerbild anhand der Künstler-ID zurück. */
function artistImageUrl(int $artistId, string $size = 'medium'): string
{
    $sizesToTry = array_unique([$size, 'medium', 'square-medium', 'square-thumb']);

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/artists/' . $trySize . '/' . $artistId . '.jpg';
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

/** Gibt die URL zu einem Genrebild anhand der Genre-ID zurück. */
function genreImageUrl(int $genreId, string $size = 'square-medium'): string
{
    // Erst die gewünschte Größe prüfen, danach die vorhandenen Genre-Formate.
    $sizesToTry = array_unique([$size, 'square-medium', 'square-thumbs']);

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/genres/' . $trySize . '/' . $genreId . '.jpg';
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

/** Gibt die URL zu einem Subjectbild anhand der Subject-ID zurück. */
function subjectImageUrl(int $subjectId, string $size = 'square-medium'): string
{
    // Erst die gewünschte Größe prüfen, danach die vorhandenen Subject-Formate.
    $sizesToTry = array_unique([$size, 'square-medium', 'square-thumbs']);

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/subjects/' . $trySize . '/' . $subjectId . '.jpg';
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

/** Prüft, ob eine Bild-URL auf das gemeinsame Platzhalterbild zeigt. */
function isPlaceholderImageUrl(string $imageUrl): bool
{
    // Nur den URL-Pfad vergleichen, damit Query-Parameter das Ergebnis nicht verändern.
    $path = parse_url($imageUrl, PHP_URL_PATH);
    return str_ends_with(is_string($path) ? $path : '', '/images/placeholder.jpg');
}

/** Gibt die Detail-URL für ein Kunstwerk zurück. */
function artworkDetailUrl(int $artworkId): string
{
    return base_url('pages/single-artwork.php') . '?id=' . urlencode((string) $artworkId);
}

/** Gibt die Detail-URL für einen Künstler zurück. */
function artistDetailUrl(int $artistId): string
{
    return base_url('pages/single-artist.php') . '?id=' . urlencode((string) $artistId);
}

/** Gibt die Detail-URL für ein Genre zurück. */
function genreDetailUrl(int $genreId): string
{
    return base_url('pages/single-genre.php') . '?id=' . urlencode((string) $genreId);
}

/** Gibt die Detail-URL für ein Subject zurück. */
function subjectDetailUrl(int $subjectId): string
{
    return base_url('pages/single-subject.php') . '?id=' . urlencode((string) $subjectId);
}