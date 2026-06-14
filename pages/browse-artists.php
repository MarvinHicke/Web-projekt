<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$pageTitle = 'Künstler durchsuchen';

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$sort = safeParam((string) ($_GET['sort'] ?? 'firstName'), ['firstName', 'lastName'], 'firstName');
$direction = safeParam(strtolower((string) ($_GET['direction'] ?? 'asc')), ['asc', 'desc'], 'asc');

try {
    $db = new dbaccess();
    $db->connect();
    $artistRepository = new artistRepository($db);
    $artists = $artistRepository->getAllSorted($sort, $direction);
} catch (Throwable $e) {
    die($e->getMessage());
}

require_once __DIR__.'/../includes/header.php';

?>

<section class="page-heading">
    <h1>Künstler durchsuchen</h1>
    <p>Entdecken sie Künstler*innen</p>
</section>

<section class="sort-panel" aria-label="Sortierung der Artists">
    <form method="get" action="<?= e(base_url('pages/browse-artists.php')); ?>">
        <label for="sort">Sortieren nach</label>
        <select id="sort" name="sort">
            <option value="firstName" <?= $sort === 'firstName' ? 'selected' : ''; ?>>Vorname</option>
            <option value="lastName" <?= $sort === 'lastName' ? 'selected' : ''; ?>>Nachname</option>
        </select>

        <label for="direction">Richtung</label>
        <select id="direction" name="direction">
            <option value="asc" <?= $direction === 'asc' ? 'selected' : ''; ?>>Aufsteigend</option>
            <option value="desc" <?= $direction === 'desc' ? 'selected' : ''; ?>>Absteigend</option>
        </select>

        <button type="submit">Anwenden</button>
    </form>
</section>

<?php if (empty($artists)): ?>
    <section class="message">
        Es wurden keine Künstler gefunden.
    </section>
<?php else: ?>
    <section class="artist-card-grid" aria-label="Liste der Artists">
        <?php foreach ($artists as $artist): ?>
            <?php
            $artistId = $artist->getId();

            $artistName = trim(
                    ($artist->getFirstName() ?? '') . ' ' . ($artist->getLastName() ?? '')
            );

            if ($artistName === '') {
                $artistName = 'Unbekannter Künstler';
            }

            $imageFileName = $artist->getImagefilename();

            $imageUrl = $imageFileName
                    ? artistImageUrl($imageFileName, 'square-small')
                    : base_url('images/placeholder.jpg');
            ?>

            <article class="artist-card-link-wrapper">
                <a class="artist-card-link" href="<?= e(artistDetailUrl($artistId)); ?>">
                    <img
                            src="<?= e($imageUrl); ?>"
                            alt="<?= e($artistName); ?>"
                            class="artist-card-image"
                    >

                    <div class="artist-card-content">

                        <h2><a href="<?= e(artistDetailUrl($artistId)); ?>"><?= e($artistName); ?></a></h2>
                        <div class="result-actions">
                            <a class="btn btn-sm btn-primary" href="<?= e(artistDetailUrl($artistId)); ?>">Ansehen</a>
                            <?php if (in_array((int) $artistId, $favoriteArtistIds, true)): ?>
                                <a class="btn btn-sm btn-warning" href="<?= e(base_url('pages/favorites.php')); ?>">In Favoriten</a>
                            <?php else: ?>
                                <a class="btn btn-sm btn-outline-primary"
                                   href="<?= e(base_url('pages/add-favorite.php') . '?type=artist&id=' . $artistId . '&redirect=browse-artists.php'); ?>">
                                    Zu Favoriten
                                </a>
                            <?php endif; ?>
                        </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
