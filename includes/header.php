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

    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Theme / 기존 스타일 -->
    <link rel="stylesheet" href="assets/css/theme.css">

    <!-- 너가 추가할 components -->
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>

<header class="site-header">
    <a class="logo" href="index.php">Art Gallery</a>

    <nav class="main-nav" aria-label="Main navigation">
        <a href="index.php">Home</a>
        <a href="browse-artworks.php">Browse Artworks</a>
    </nav>

    <form class="global-search" action="search-results.php" method="get" role="search">
        <input 
            type="search" 
            name="q" 
            value="<?= e($currentSearch); ?>" 
            placeholder="Search artworks or artists"
        >
    </form>
</header>