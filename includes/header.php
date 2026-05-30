<?php
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Art Gallery';
$currentSearch = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$currentUserName = $_SESSION['username']
    ?? $_SESSION['user']['username']
    ?? $_SESSION['userName']
    ?? null;

$currentUserType = $_SESSION['user_type']
    ?? $_SESSION['user']['type']
    ?? $_SESSION['type']
    ?? null;

$isLoggedIn = !empty($currentUserName);
$isAdmin = strtolower((string) $currentUserType) === 'admin';
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/theme_bootswatch.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/components.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css')?>">



</head>
<body>

<header class="site-header">

    <div class="header-top">
        <a class="logo" href="<?= base_url('index.php') ?>">Art Gallery</a>

        <!-- Utility-Menü -->
       <nav class="utility-nav" aria-label="Hilfsnavigation">
    <ul>
        <li><a href="favorites.php">Favoriten anzeigen</a></li>

        <?php if ($isLoggedIn): ?>
            <li class="user-status">
                Angemeldet als:
                <strong><?= e((string) $currentUserName); ?></strong>
            </li>

            <li><a href="account.php">Mein Konto</a></li>

            <?php if ($isAdmin): ?>
                <li><a href="manage-users.php">Benutzer verwalten</a></li>
            <?php endif; ?>

            <li><a href="logout.php">Abmelden</a></li>
        <?php else: ?>
            <li class="user-status">Nicht angemeldet</li>
            <li><a href="register.php">Registrieren</a></li>
            <li><a href="login.php">Anmelden</a></li>
        <?php endif; ?>
    </ul>
</nav>
</nav>
    </div>

    <!-- Primäre Navigation -->
    <nav class="main-nav" aria-label="Hauptnavigation">
        <ul>
            <li><a href="<?= base_url('index.php') ?>">Startseite</a></li>
            <li><a href="<?= base_url('pages/about.php') ?>">Über uns</a></li>
            <li><a href="<?= base_url('pages/advanced-search.php') ?>">Erweiterte Suche</a></li>

            <li class="dropdown">
                <a href="<?= base_url('pages/artworks.php')?>">Durchsuchen</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= base_url('pages/browse-artworks.php')?>">Kunstwerke</a></li>
                    <li><a href="<?= base_url('pages/browse-artists.php')?>">Künstler</a></li>
                    <li><a href="<?= base_url('pages/browse-museums.php')?>">Museen</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <form class="global-search" action="<?= base_url('search-results.php')?>" method="get" role="search">
        <label class="sr-only" for="global-search-input">Kunstwerke oder Künstler suchen</label>
        <input
            id="global-search-input"
            name="q"
            type="search"
            minlength="3"
            value="<?= e($currentSearch); ?>"
            placeholder="Suche min. 3 Zeichen"
        >
        <button type="submit">Suchen</button>
    </form>

</header>

<main class="page-shell">