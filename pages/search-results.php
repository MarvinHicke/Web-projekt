<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../model/helper.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$pageTitle = 'Suchergebnisse';
 
$query            = trim((string) ($_GET['q'] ?? ''));
$artistDirection  = safeParam(strtolower((string) ($_GET['artist_direction'] ?? 'asc')), ['asc', 'desc'], 'asc');
$artworkSort      = safeParam((string) ($_GET['artwork_sort'] ?? 'title'), ['title', 'artist', 'year'], 'title');
$artworkDirection = safeParam(strtolower((string) ($_GET['artwork_direction'] ?? 'asc')), ['asc', 'desc'], 'asc');
 
$artistResults  = [];
$artworkResults = [];

$_SESSION['favorites'] ??= [];
$_SESSION['favorites']['artists'] ??= [];
$_SESSION['favorites']['artworks'] ??= [];

$favoriteArtistIds  = array_map('intval', $_SESSION['favorites']['artists']);
$favoriteArtworkIds = array_map('intval', $_SESSION['favorites']['artworks']);
 
if (mb_strlen($query) >= 3) {
    try {
        $db = new dbaccess();
        $db->connect();
 
        $artworkRepo = new artworkRepository($db);
        $artistRepo  = new artistRepository($db);
 
        // Search artists by last name
        $artistObjs = $artistRepo->searchByLastName($query);
        foreach ($artistObjs as $artist) {
            $artistResults[] = [
                'ArtistID'  => $artist->getId(),
                'FirstName' => $artist->getFirstName(),
                'LastName'  => $artist->getLastName(),
            ];
        }
 
        // Search artworks by title
        $artworkObjs = $artworkRepo->searchByTitle($query);
        foreach ($artworkObjs as $artwork) {
            $artworkResults[] = [
                'ArtWorkID'     => $artwork->getArtworkid(),
                'Title'         => $artwork->getTitle(),
                'FirstName'     => $artwork->getFirstName(),
                'LastName'      => $artwork->getLastName(),
                'YearOfWork'    => $artwork->getYearofwork(),
                'ImageFileName' => $artwork->getImagefilename(),
            ];
        }
 
    } catch (Exception $e) {
        // DB not available — results stay empty
    }
 
    usort($artistResults, static function (array $a, array $b) use ($artistDirection): int {
        $comparison = strcasecmp(
            (string) ($a['LastName'] ?? '') . (string) ($a['FirstName'] ?? ''),
            (string) ($b['LastName'] ?? '') . (string) ($b['FirstName'] ?? '')
        );
        return $artistDirection === 'desc' ? -$comparison : $comparison;
    });

    usort($artworkResults, static function (array $a, array $b) use ($artworkSort, $artworkDirection): int {
        if ($artworkSort === 'year') {
            $comparison = (int) ($a['YearOfWork'] ?? 0) <=> (int) ($b['YearOfWork'] ?? 0);
        } elseif ($artworkSort === 'artist') {
            $comparison = strcasecmp(
                (string) ($a['LastName'] ?? '') . (string) ($a['FirstName'] ?? ''),
                (string) ($b['LastName'] ?? '') . (string) ($b['FirstName'] ?? '')
            );
        } else {
            $comparison = strcasecmp((string) ($a['Title'] ?? ''), (string) ($b['Title'] ?? ''));
        }

        return $artworkDirection === 'desc' ? -$comparison : $comparison;
    });
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
 
        <label for="artist-direction">Künstler</label>
        <select id="artist-direction" name="artist_direction">
            <option value="asc" <?= $artistDirection === 'asc' ? 'selected' : ''; ?>>Name aufsteigend</option>
            <option value="desc" <?= $artistDirection === 'desc' ? 'selected' : ''; ?>>Name absteigend</option>
        </select>

        <label for="artwork-sort">Kunstwerke nach</label>
        <select id="artwork-sort" name="artwork_sort">
            <option value="title" <?= $artworkSort === 'title' ? 'selected' : ''; ?>>Titel</option>
            <option value="artist" <?= $artworkSort === 'artist' ? 'selected' : ''; ?>>Künstler</option>
            <option value="year" <?= $artworkSort === 'year' ? 'selected' : ''; ?>>Jahr</option>
        </select>

        <label for="artwork-direction">Richtung</label>
        <select id="artwork-direction" name="artwork_direction">
            <option value="asc" <?= $artworkDirection === 'asc' ? 'selected' : ''; ?>>Aufsteigend</option>
            <option value="desc" <?= $artworkDirection === 'desc' ? 'selected' : ''; ?>>Absteigend</option>
        </select>
 
        <button type="submit">Suchen</button>
    </form>
</section>
 
<?php if ($query !== '' && mb_strlen($query) < 3): ?>
    <div class="message">Bitte geben Sie mindestens drei Zeichen ein.</div>
<?php endif; ?>

<?php if ($query === '' && $searchType === ''): ?>
    <div class="message">Bitte geben Sie einen Suchbegriff ein.</div>
<?php endif; ?>

<?php if ($searchType !== '' || mb_strlen($query) >= 3): ?>

    <?php if (empty($artistResults) && empty($artworkResults)): ?>
        <div class="message">Keine Künstler oder Kunstwerke für diese Suche gefunden.</div>
    <?php else: ?>

        <?php if (!empty($artistResults)): ?>
            <h2>Künstler</h2>
            <div style="margin-bottom: 2rem;">
                <?php foreach ($artistResults as $artist): ?>
                    <article class="mini-card" style="margin-bottom: 1rem;">
                        <?php
                        $bildPfad = Helper::getImagePath($artist->getId(), 'artists', 'square-medium');
                        ?>
                        <img src="../<?php echo htmlspecialchars($bildPfad); ?>" alt="Portrait des Künstlers">
                        <div>
                            <h3>
                                <a href="<?= e(artistDetailUrl($artistId)); ?>">
                                    <?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?>
                                </a>
                            </h3>
                            <div class="result-actions">
                                <a class="btn btn-sm btn-primary" href="<?= e(artistDetailUrl($artistId)); ?>">Ansehen</a>
                                <?php if (in_array($artistId, $favoriteArtistIds, true)): ?>
                                    <a class="btn btn-sm btn-warning" href="<?= e(base_url('pages/favorites.php')); ?>">In Favoriten</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= e(base_url('pages/add-favorite.php') . '?type=artist&id=' . $artistId); ?>">
                                        Zu Favoriten
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($artworkResults)): ?>
            <h2>Kunstwerke</h2>
            <div>
                <?php foreach ($artworkResults as $artwork): ?>
                    <article class="mini-card" style="margin-bottom: 1rem;">
                        <?php
                        $dateinameOhneEndung = str_replace('.jpg', '', $artwork->getImagefilename());
                        $bildPfad = Helper::getImagePath($dateinameOhneEndung, 'works', 'square-small');

                        $kuenstler = isset($artistRepo) ? $artistRepo->getById($artwork->getArtistId()) : null;
                        $kuenstlerName = $kuenstler ? $kuenstler->getFirstName() . ' ' . $kuenstler->getLastName() : 'Unbekannter Künstler';
                        ?>
                        <img src="../<?php echo htmlspecialchars($bildPfad); ?>" alt="Bild des Kunstwerks">
                        <div>
                            <h3><a href="<?= e(artworkDetailUrl($artworkId)); ?>"><?= e($title); ?></a></h3>
                            <p><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></p>
                            <p><?= e($year !== '' ? $year : 'Jahr unbekannt'); ?></p>
                            <div class="result-actions">
                                <a class="btn btn-sm btn-primary" href="<?= e(artworkDetailUrl($artworkId)); ?>">Ansehen</a>
                                <?php if (in_array($artworkId, $favoriteArtworkIds, true)): ?>
                                    <a class="btn btn-sm btn-warning" href="<?= e(base_url('pages/favorites.php')); ?>">In Favoriten</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= e(base_url('pages/add-favorite.php') . '?type=artwork&id=' . $artworkId); ?>">
                                        Zu Favoriten
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>

<?php
if (isset($db))
{
    $db->close();
}
require_once __DIR__ . '/../includes/footer.php';
?>