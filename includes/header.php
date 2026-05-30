<?php
require_once __DIR__ . '/helpers.php';

$pageTitle = $pageTitle ?? 'Art Gallery';
$currentSearch = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
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
                <li><a href="<?= base_url('pages/favorites.php') ?>">Favoriten anzeigen</a></li>
                <li><a href="<?= base_url('pages/account.php') ?>">Mein Konto</a></li>
                <li><a href="<?= base_url('pages/manage-users.php') ?>">Benutzer verwalten</a></li>
                <li><a href="<?= base_url('pages/register.php') ?>">Registrieren</a></li>
                <li><a href="<?= base_url('pages/login.php') ?>">Anmelden</a></li>
            </ul>
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
                    <li><a href="<?= base_url('pages/browse-genre.php')?>">Genre</a></li>
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