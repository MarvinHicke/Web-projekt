<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';

$pageTitle = 'Kunstwerke durchsuchen';

$sort = safeParam((string) ($_GET['sort'] ?? 'title'), ['title', 'artist', 'year'], 'title');
$direction = safeParam(strtolower((string) ($_GET['direction'] ?? 'asc')), ['asc', 'desc'], 'asc');

try {
    $db = new dbaccess();
    $db->connect();
    $artworkRepository = new artworkRepository($db);
    $artworks = $artworkRepository->getAllSorted($sort, $direction);
} catch (Exception $e) {
    $artworks = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <h1>Kunstwerke durchsuchen</h1>
    <p>Hier finden Sie alle Kunstwerke. Die Liste kann nach Titel, Künstler oder Jahr sortiert werden.</p>
</section>

<section class="sort-panel" aria-label="Sortierung der Kunstwerke">
    <form method="get" action="<?= e(base_url('pages/browse-artworks.php')); ?>">
        <label for="sort">Sortieren nach</label>
        <select id="sort" name="sort">
            <option value="title" <?= $sort === 'title' ? 'selected' : ''; ?>>Titel</option>
            <option value="artist" <?= $sort === 'artist' ? 'selected' : ''; ?>>Künstler</option>
            <option value="year" <?= $sort === 'year' ? 'selected' : ''; ?>>Jahr</option>
        </select>

        <label for="direction">Richtung</label>
        <select id="direction" name="direction">
            <option value="asc" <?= $direction === 'asc' ? 'selected' : ''; ?>>Aufsteigend</option>
            <option value="desc" <?= $direction === 'desc' ? 'selected' : ''; ?>>Absteigend</option>
        </select>

        <button type="submit">Anwenden</button>
    </form>
</section>

<?php if (empty($artworks)): ?>
    <section class="message">
        Es wurden keine Kunstwerke gefunden.
    </section>
<?php else: ?>
    <section class="artwork-card-grid" aria-label="Liste der Kunstwerke">
        <?php foreach ($artworks as $artwork): ?>
            <?php
            $artworkId = $artwork->getArtworkid();
            $title = $artwork->getTitle();
            $artistName = trim(
                ($artwork->getFirstName() ?? '') . ' ' . ($artwork->getLastName() ?? '')
            );
            $year = $artwork->getYearofwork();
            $imageFileName = $artwork->getImagefilename();
            ?>

            <article class="artwork-card-link-wrapper">
                <a class="artwork-card-link" href="<?= e(artworkDetailUrl($artworkId)); ?>">
                    <img
                        src="<?= e(artworkImageUrl($imageFileName, 'square-small')); ?>"
                        alt="<?= e($title); ?>"
                        class="artwork-card-image"
                    >

                    <div class="artwork-card-content">
                        <h2><?= e($title); ?></h2>
                        <p><strong>Künstler:</strong> <?= e($artistName !== '' ? $artistName : 'Unbekannt'); ?></p>
                        <p><strong>Jahr:</strong> <?= e($year !== '' ? $year : 'Unbekannt'); ?></p>
                        <span class="text-link">Einzelansicht öffnen</span>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
