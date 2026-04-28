<?php
require_once __DIR__ . '/helpers.php';

$pageTitle = $pageTitle ?? 'Art Gallery';
$currentSearch = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> /* Verlinkt bootstrap */
    <link rel="stylesheet" href="assets/css/theme.css"> /* Verlinkt bootswatch (Design) */
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">Art Gallery</a>

    <nav class="main-nav" aria-label="Main navigation">
        <a href="index.php">Home</a>
        <a href="browse-artworks.php">Browse Artworks</a>
    </nav>

    <form class="global-search" action="search-results.php" method="get" role="search">
        <label class="sr-only" for="global-search-input">Search artworks or artists</label>
        <input
            id="global-search-input"
            name="q"
            type="search"
            minlength="3"
            value="<?= e($currentSearch); ?>"
            placeholder="Search min. 3 chars"
        >
        <button type="submit">Search</button>
    </form>
</header>

<main class="page-shell">
