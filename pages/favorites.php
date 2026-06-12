<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../repositories/artworkRepository.php';
require_once __DIR__ . '/../repositories/artistRepository.php';

$pageTitle = 'Favoriten';

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if (!isset($_SESSION['favorites']))
{
    $_SESSION['favorites'] = [];
}

if (!isset($_SESSION['favorites']['artworks']))
{
    $_SESSION['favorites']['artworks'] = [];
}

if (!isset($_SESSION['favorites']['artists']))
{
    $_SESSION['favorites']['artists'] = [];
}

$favoriteArtworkIds = $_SESSION['favorites']['artworks'];
$favoriteArtistIds = $_SESSION['favorites']['artists'];

$favoriteArtworks = [];
$favoriteArtists = [];

if (!empty($favoriteArtworkIds) || !empty($favoriteArtistIds))
{
    $db = new dbaccess();
    $db->connect();

    $artworkRepository = new artworkRepository($db);
    $artistRepository = new artistRepository($db);

    foreach ($favoriteArtworkIds as $artworkId)
    {
        $artwork = $artworkRepository->getById((int) $artworkId);

        if ($artwork !== null)
        {
            $favoriteArtworks[] = $artwork;
        }
    }

    foreach ($favoriteArtistIds as $artistId)
    {
        $artist = $artistRepository->getById((int) $artistId);

        if ($artist !== null)
        {
            $favoriteArtists[] = $artist;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

    <section class="page-heading">
        <h1>Favoriten</h1>
        <p>Hier sehen Sie Ihre favorisierten Künstler und Kunstwerke.</p>
    </section>

    <section class="result-grid" aria-label="Favoritenlisten">
        <div class="result-column">
            <h2>Favorisierte Künstler</h2>

            <?php if (empty($favoriteArtists)): ?>
                <?php
                $alertType = 'info';
                $alertMessage = 'Du hast noch keine Künstler favorisiert.';
                include __DIR__ . '/../components/alert-box.php';
                ?>
            <?php else: ?>
                <?php foreach ($favoriteArtists as $artist): ?>
                    <?php
                    $artistId = (int) $artist->getId();
                    $artistName = trim((string) $artist->getFirstName() . ' ' . (string) $artist->getLastName());
                    ?>

                    <article class="mini-card">
                        <img
                                src="<?= e(artistImageUrl($artistId, 'medium')); ?>"
                                alt="<?= e($artistName); ?>"
                        >

                        <div>
                            <h3>
                                <a href="<?= e(artistDetailUrl($artistId)); ?>">
                                    <?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?>
                                </a>
                            </h3>

                            <p>Artist ID: <?= e((string) $artistId); ?></p>

                            <a
                                    class="btn btn-danger btn-sm mt-2"
                                    href="<?= e(base_url('pages/remove-favorite.php') . '?type=artist&id=' . urlencode((string) $artistId)); ?>"
                            >
                                Aus Favoriten entfernen
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="result-column">
            <h2>Favorisierte Kunstwerke</h2>

            <?php if (empty($favoriteArtworks)): ?>
                <?php
                $alertType = 'info';
                $alertMessage = 'Du hast noch keine Kunstwerke favorisiert.';
                include __DIR__ . '/../components/alert-box.php';
                ?>
            <?php else: ?>
                <?php foreach ($favoriteArtworks as $artwork): ?>
                    <?php
                    $artworkId = (int) $artwork->getArtworkid();
                    $title = (string) $artwork->getTitle();
                    $year = (string) $artwork->getYearofwork();
                    $imageFileName = (string) $artwork->getImagefilename();
                    $artistName = trim((string) $artwork->getFirstName() . ' ' . (string) $artwork->getLastName());
                    ?>

                    <article class="mini-card">
                        <img
                                src="<?= e(artworkImageUrl($imageFileName, 'medium')); ?>"
                                alt="<?= e($title); ?>"
                        >

                        <div>
                            <h3>
                                <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                                    <?= e($title !== '' ? $title : 'Unbekanntes Kunstwerk'); ?>
                                </a>
                            </h3>
                            <p><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></p>
                            <p><?= e($year !== '' ? $year : 'Jahr unbekannt'); ?></p>

                            <a
                                    class="btn btn-danger btn-sm mt-2"
                                    href="<?= e(base_url('pages/remove-favorite.php') . '?type=artwork&id=' . urlencode((string) $artworkId)); ?>"
                            >
                                Aus Favoriten entfernen
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>