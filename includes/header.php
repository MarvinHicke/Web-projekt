<?php
require_once __DIR__ . '/init.php';

$pageTitle = $pageTitle ?? 'Art Gallery';
$currentSearch = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>

    <link rel="stylesheet" href="<?= e(base_url('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/theme_bootswatch.css')); ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/components.css')); ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/styles.css')); ?>?v=5">
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <a class="logo" href="<?= e(base_url('index.php')); ?>">Art Gallery</a>

        <nav class="utility-nav" aria-label="Hilfsnavigation">
            <ul>
                <li><a href="<?= e(base_url('pages/favorites.php')); ?>">Favoriten</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?= e(base_url('pages/account.php')); ?>">Mein Konto: <?= e(getCurrentUsername()); ?></a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= e(base_url('pages/manage-users.php')); ?>">Benutzer verwalten</a></li>
                    <?php endif; ?>
                    <li><a href="<?= e(base_url('pages/logout.php')); ?>">Abmelden</a></li>
                <?php else: ?>
                    <li><a href="<?= e(base_url('pages/register.php')); ?>">Registrieren</a></li>
                    <li><a href="<?= e(base_url('pages/login.php')); ?>">Anmelden</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <nav class="main-nav" aria-label="Hauptnavigation">
        <ul>
            <li><a href="<?= e(base_url('index.php')); ?>">Startseite</a></li>
            <li><a href="<?= e(base_url('pages/about.php')); ?>">Über uns</a></li>
            <li><a href="<?= e(base_url('pages/advanced-search.php')); ?>">Erweiterte Suche</a></li>

            <li class="dropdown">
                <a
                    href="<?= e(base_url('pages/browse-artworks.php')); ?>"
                    aria-haspopup="true"
                    aria-expanded="false"
                >Durchsuchen</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= e(base_url('pages/browse-artworks.php')); ?>">Kunstwerke</a></li>
                    <li><a href="<?= e(base_url('pages/browse-artists.php')); ?>">Künstler</a></li>
                    <li><a href="<?= e(base_url('pages/browse-genre.php')); ?>">Genres</a></li>
                    <li><a href="<?= e(base_url('pages/browse-subject.php')); ?>">Themen</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <form class="global-search" action="<?= e(base_url('pages/search-results.php')); ?>" method="get" role="search">
        <label class="sr-only" for="global-search-input">Kunstwerke oder Künstler suchen</label>
        <input
            id="global-search-input"
            class="form-control"
            name="q"
            type="search"
            minlength="3"
            value="<?= e($currentSearch); ?>"
            placeholder="Suche min. 3 Zeichen"
        >
        <button class="btn btn-primary" type="submit">Suchen</button>
    </form>
</header>

<main class="page-shell">
