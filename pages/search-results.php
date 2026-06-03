<?php
require_once __DIR__ . '/../includes/bootstrap.php';
/*
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
*/
require_once __DIR__ . '/../includes/mock-data.php';

$pageTitle = 'Suchergebnisse';

$query = trim((string) ($_GET['q'] ?? ''));
$sort = safeParam((string) ($_GET['sort'] ?? 'relevance'), ['relevance', 'title', 'artist'], 'relevance');

$artistResults = [];
$artworkResults = [];

if (mb_strlen($query) >= 3) {
    $queryLower = mb_strtolower($query);

    foreach ($artists as $artist) {
        $lastName = mb_strtolower((string) ($artist['last_name'] ?? $artist['LastName'] ?? ''));

        if (str_contains($lastName, $queryLower)) {
            $artistResults[] = $artist;
        }
    }

    foreach ($artworks as $artwork) {
        $title = mb_strtolower((string) ($artwork['title'] ?? $artwork['Title'] ?? ''));
        $artistLastName = mb_strtolower((string) ($artwork['artist_last_name'] ?? $artwork['LastName'] ?? ''));

        if (str_contains($title, $queryLower) || str_contains($artistLastName, $queryLower)) {
            $artworkResults[] = $artwork;
        }
    }

    if ($sort === 'title') {
        usort($artworkResults, static function (array $a, array $b): int {
            return strcmp((string) ($a['title'] ?? $a['Title'] ?? ''), (string) ($b['title'] ?? $b['Title'] ?? ''));
        });
    }

    if ($sort === 'artist') {
        usort($artworkResults, static function (array $a, array $b): int {
            return strcmp((string) ($a['artist_last_name'] ?? $a['LastName'] ?? ''), (string) ($b['artist_last_name'] ?? $b['LastName'] ?? ''));
        });
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <h1>Suchergebnisse</h1>
    <p>Die Suche ist global erreichbar und sucht ab mindestens drei Zeichen nach Künstlernachnamen oder Kunstwerktiteln.</p>
</section>

<section class="sort-panel" aria-label="Suchformular">
    <form method="get" action="<?= e(base_url('pages/search-results.php')); ?>">
        <label for="search-page-input">Suchbegriff</label>

        <input
            id="search-page-input"
            type="search"
            name="q"
            minlength="3"
            value="<?= e($query); ?>"
            placeholder="z. B. Gogh oder Mona"
        >

        <label for="sort">Sortierung</label>
        <select id="sort" name="sort">
            <option value="relevance" <?= $sort === 'relevance' ? 'selected' : ''; ?>>Relevanz</option>
            <option value="title" <?= $sort === 'title' ? 'selected' : ''; ?>>Titel</option>
            <option value="artist" <?= $sort === 'artist' ? 'selected' : ''; ?>>Künstler</option>
        </select>

        <button type="submit">Suchen</button>
    </form>
</section>

<?php if ($query !== '' && mb_strlen($query) < 3): ?>
    <section class="message">Bitte geben Sie mindestens drei Zeichen ein.</section>
<?php endif; ?>

<?php if ($query === ''): ?>
    <section class="message">Bitte geben Sie einen Suchbegriff ein.</section>
<?php endif; ?>

<?php if (mb_strlen($query) >= 3): ?>
    <section class="result-grid" aria-label="Suchergebnisse">
        <div class="result-column">
            <h2>Künstler</h2>

            <?php if (empty($artistResults)): ?>
                <p>Keine Künstler gefunden.</p>
            <?php endif; ?>

            <?php foreach ($artistResults as $artist): ?>
                <?php
                $artistId = (int) ($artist['id'] ?? $artist['ArtistID'] ?? 0);
                $artistName = trim((string) ($artist['first_name'] ?? $artist['FirstName'] ?? '') . ' ' . (string) ($artist['last_name'] ?? $artist['LastName'] ?? ''));
                $image = (string) ($artist['image'] ?? '');
                ?>

                <article class="mini-card">
                    <img src="<?= e($image !== '' ? base_url($image) : artistImageUrl($artistId, 'medium')); ?>" alt="<?= e($artistName); ?>">

                    <div>
                        <h3><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></h3>
                        <p>Platzhalter für Zusatzinformationen.</p>
                        <a href="<?= e(artistDetailUrl($artistId)); ?>">Künstler öffnen</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="result-column">
            <h2>Kunstwerke</h2>

            <?php if (empty($artworkResults)): ?>
                <p>Keine Kunstwerke gefunden.</p>
            <?php endif; ?>

            <?php foreach ($artworkResults as $artwork): ?>
                <?php
                $artworkId = (int) ($artwork['id'] ?? $artwork['ArtWorkID'] ?? 0);
                $title = (string) ($artwork['title'] ?? $artwork['Title'] ?? 'Unbekanntes Kunstwerk');
                $artistName = trim((string) ($artwork['artist_first_name'] ?? $artwork['FirstName'] ?? '') . ' ' . (string) ($artwork['artist_last_name'] ?? $artwork['LastName'] ?? ''));
                $year = (string) ($artwork['year'] ?? $artwork['YearOfWork'] ?? '');
                $image = (string) ($artwork['image'] ?? $artwork['ImageFileName'] ?? '');
                ?>

                <article class="mini-card">
                    <img src="<?= e($image !== '' ? base_url($image) : artworkImageUrl('', 'square-small')); ?>" alt="<?= e($title); ?>">

                    <div>
                        <h3><?= e($title); ?></h3>
                        <p><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></p>
                        <p><?= e($year !== '' ? $year : 'Jahr unbekannt'); ?></p>
                        <a href="<?= e(artworkDetailUrl($artworkId)); ?>">Kunstwerk öffnen</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
