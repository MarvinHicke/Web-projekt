<?php

/** Escape output safely */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Strip HTML tags from DB content (e.g. <em>, <p>) for plain text output */
function cleanHtml(?string $text): string
{
    return trim(strip_tags((string) $text));
}

/** Validate a parameter against an allowed list */
function safeParam(string $value, array $allowed, string $fallback): string
{
    return in_array($value, $allowed, true) ? $value : $fallback;
}

/**
 * Returns URL for an artwork image, trying multiple size folders as fallback.
 * Fixes the placeholder issue when an image only exists in some sizes.
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

    // Try requested size first, then fallbacks in order
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

/** Returns URL for an artist image by ID */
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

/** Returns URL for a genre image by ID */
function genreImageUrl(int $genreId): string
{
    $sizesToTry = ['square-medium', 'square-thumbs'];

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/genres/' . $trySize . '/' . $genreId . '.jpg';
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

/** Returns URL for a subject image by ID */
function subjectImageUrl(int $subjectId): string
{
    $sizesToTry = ['square-medium', 'square-thumbs'];

    foreach ($sizesToTry as $trySize) {
        $relativePath = 'images/subjects/' . $trySize . '/' . $subjectId . '.jpg';
        $absolutePath = __DIR__ . '/../' . $relativePath;
        if (file_exists($absolutePath)) {
            return base_url($relativePath);
        }
    }

    return base_url('images/placeholder.jpg');
}

function artworkDetailUrl(int $artworkId): string
{
    return base_url('pages/single-artwork.php') . '?id=' . urlencode((string) $artworkId);
}

function artistDetailUrl(int $artistId): string
{
    return base_url('pages/single-artist.php') . '?id=' . urlencode((string) $artistId);
}

function genreDetailUrl(int $genreId): string
{
    return base_url('pages/single-genre.php') . '?id=' . urlencode((string) $genreId);
}

function subjectDetailUrl(int $subjectId): string
{
    return base_url('pages/single-subject.php') . '?id=' . urlencode((string) $subjectId);
}
