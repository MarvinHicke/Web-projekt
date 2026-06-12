<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../model/helper.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$pageTitle = 'Suchergebnisse';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$direction = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
$searchType = isset($_GET['search_type']) ? $_GET['search_type'] : '';

if ($query !== '')
{
    $searchType = '';
}

$artistResults = [];
$artworkResults = [];

try
{
    $db = new dbaccess();
    $db->connect();

    $artworkRepo = new artworkRepository($db);
    $artistRepo  = new artistRepository($db);

    if ($searchType === 'artist') {
        $name = isset($_GET['artist_name']) ? trim($_GET['artist_name']) : '';
        $nationality = isset($_GET['nationality']) ? $_GET['nationality'] : '';
        $yearMin = !empty($_GET['year_min']) ? (int)$_GET['year_min'] : null;
        $yearMax = !empty($_GET['year_max']) ? (int)$_GET['year_max'] : null;

        $artistResults = $artistRepo->advancedSearch($name, $yearMin, $yearMax, $nationality, $direction);
    }
    elseif ($searchType === 'artwork')
    {
        $title = isset($_GET['artwork_title']) ? trim($_GET['artwork_title']) : '';
        $genreId = !empty($_GET['genre']) ? (int)$_GET['genre'] : null;
        $yearMin = !empty($_GET['year_min']) ? (int)$_GET['year_min'] : null;
        $yearMax = !empty($_GET['year_max']) ? (int)$_GET['year_max'] : null;

        $artworkResults = $artworkRepo->advancedSearch($title, $yearMin, $yearMax, $genreId, $sort, $direction);

    }
    elseif (mb_strlen($query) >= 3)
    {
        $artistResults  = $artistRepo->searchByLastName($query, $direction);
        $artworkResults = $artworkRepo->searchByTitle($query, $sort, $direction);
    }

} catch (Exception $e)
{}

require_once __DIR__ . '/../includes/header.php';
?>

    <section class="page-heading">
        <h1>Suchergebnisse</h1>
        <p>Die Suche ist global erreichbar und sucht ab mindestens drei Zeichen nach Künstlernachnamen oder Kunstwerktiteln.</p>
    </section>

    <section class="sort-panel" aria-label="Suchformular">
        <form method="get" action="search-results.php">

            <?php
            if ($searchType !== '')
            {
                foreach ($_GET as $key => $value)
                {
                    if ($key !== 'q' && $key !== 'sort' && $value !== '')
                    {
                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                    }
                }
            }
            ?>

            <label for="search-page-input">Suchbegriff</label>
            <input id="search-page-input" type="search" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="z. B. Gogh oder Mona">

            <label for="sort">Sortierung</label>
            <select id="sort" name="sort">
                <option value="title"  <?php if($sort === 'title') echo 'selected'; ?>>Titel</option>
                <option value="artist" <?php if($sort === 'artist') echo 'selected'; ?>>Künstler</option>
                <option value="year"   <?php if($sort === 'year') echo 'selected'; ?>>Jahr</option>
            </select>

            <button type="submit">Suchen</button>
        </form>
    </section>

    <hr>

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
                            <h3><?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()); ?></h3>
                            <a href="single-artist.php?id=<?php echo $artist->getId(); ?>">Künstler öffnen</a>
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
                            <h3><?php echo htmlspecialchars($artwork->getTitle()); ?></h3>
                            <p><?php echo htmlspecialchars($kuenstlerName); ?></p>
                            <p><?php echo htmlspecialchars($artwork->getYearofwork()); ?></p>
                            <a href="single-artwork.php?id=<?php echo $artwork->getArtworkid(); ?>">Kunstwerk öffnen</a>
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