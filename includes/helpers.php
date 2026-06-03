<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function safeParam(string $value, array $allowed, string $fallback): string
{
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function artworkImageUrl(?string $imageFileName, string $size = 'square-small'): string
{
    $fileName = trim((string) $imageFileName);

    if ($fileName === '') {
        return base_url('images/placeholder.jpg');
    }

    if (!str_ends_with(strtolower($fileName), '.jpg')) {
        $fileName .= '.jpg';
    }

    $relativePath = 'images/works/' . $size . '/' . $fileName;
    $absolutePath = __DIR__ . '/../' . $relativePath;

    if (!file_exists($absolutePath)) {
        return base_url('images/placeholder.jpg');
    }

    return base_url($relativePath);
}

function artistImageUrl(int $artistId, string $size = 'medium'): string
{
    $relativePath = 'images/artists/' . $size . '/' . $artistId . '.jpg';
    $absolutePath = __DIR__ . '/../' . $relativePath;

    if (!file_exists($absolutePath)) {
        return base_url('images/placeholder.jpg');
    }

    return base_url($relativePath);
}

function artworkDetailUrl(int $artworkId): string
{
    return base_url('pages/single-artwork.php') . '?id=' . urlencode((string) $artworkId);
}

function artistDetailUrl(int $artistId): string
{
    return base_url('pages/single-artist.php') . '?id=' . urlencode((string) $artistId);
}
