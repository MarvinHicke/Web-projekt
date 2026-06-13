<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Erweiterte Suche';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">Erweiterte Funktion</p>
    <h1>Erweiterte Suche</h1>
    <p class="mb-0">
        Die erweiterte Suche ist in dieser Projektversion noch nicht verfügbar.
        Nutzen Sie bis dahin die globale Suche oder durchsuchen Sie die vorhandenen Kategorien.
    </p>
</section>

<section class="content-card">
    <h2>Verfügbare Möglichkeiten</h2>

    <form
        class="advanced-search-fallback"
        action="<?= e(base_url('pages/search-results.php')); ?>"
        method="get"
        role="search"
    >
        <div>
            <label class="form-label fw-semibold" for="advanced-fallback-query">
                Künstler oder Kunstwerk suchen
            </label>
            <input
                class="form-control"
                id="advanced-fallback-query"
                name="q"
                type="search"
                minlength="3"
                required
                placeholder="Mindestens drei Zeichen"
            >
        </div>
        <button class="btn btn-primary" type="submit">Suche starten</button>
    </form>

    <div class="browse-link-grid mt-4" aria-label="Kategorien durchsuchen">
        <a class="btn btn-outline-primary" href="<?= e(base_url('pages/browse-artworks.php')); ?>">Kunstwerke</a>
        <a class="btn btn-outline-primary" href="<?= e(base_url('pages/browse-artists.php')); ?>">Künstler</a>
        <a class="btn btn-outline-primary" href="<?= e(base_url('pages/browse-genre.php')); ?>">Genres</a>
        <a class="btn btn-outline-primary" href="<?= e(base_url('pages/browse-subject.php')); ?>">Themen</a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
