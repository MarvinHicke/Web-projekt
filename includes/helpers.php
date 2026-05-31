<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function artworkUrl(int $id): string
{
    return base_url('pages/browse-artworks.php?id=' . urlencode((string) $id));
}

function artistUrl(int $id): string
{
    return base_url('pages/browse-artists.php?id=' . urlencode((string) $id));
}
