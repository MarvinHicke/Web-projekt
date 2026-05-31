<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . "/../includes/bootstrap.php";
require_once __DIR__ . "/../repositories/artworkRepository.php";
require_once __DIR__ . "/../repositories/artistRepository.php";

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if(!isset($_SESSION["favorites"]))
{
    $_SESSION["favorites"] = [];
}
if(!isset($_SESSION["favorites"]["artworks"]))
{
    $_SESSION["favorites"]["artworks"] = [];
}
if(!isset($_SESSION["favorites"]["artists"]))
{
    $_SESSION["favorites"]["artists"] = [];
}
$favoriteArtworkIds = $_SESSION["favorites"]["artworks"];
$favoriteArtistIds = $_SESSION["favorites"]["artists"];
$favoriteArtworks = [];
$favoriteArtists = [];
if (!empty($favoriteArtworkIds) || !empty($favoriteArtistIds)) {
    $db = new dbaccess();
    $db->connect();
    $artworkRepository = new artworkRepository($db);
    $artistRepository = new artistRepository($db);
    foreach ($favoriteArtworkIds as $artworkId)
    {
        $artwork= $artworkRepository->getById((int) $artworkId);

        if($artwork!==null)
        {
            $favoriteArtworks[] = $artwork;
        }
    }
    foreach ($favoriteArtistIds as $artistId)
    {
        $artist= $artistRepository->getById((int) $artistId);

        if($artist!==null)
        {
            $favoriteArtists[] = $artist;
        }
    }
}
$pageTitle = "Favoriten";

require_once __DIR__ . "/../includes/header.php";
?>

<h1>Favoriten</h1>
<p>Hier werden später deine favorisierten Kunstwerke und Künstler angezeigt.</p>

<h2>Favorisierte Kunstwerke</h2>
<?php if(empty($favoriteArtworkIds)) : ?>
    <?php
    $alertType="info";
    $alertMessage="Du hast noch keine Kunstwerke favorisiert.";
    include __DIR__ . '/../components/alert-box.php';
    ?>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($favoriteArtworks as $artwork): ?>
            <?php include __DIR__ . '/../components/artwork-card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="mt-3">
        <?php foreach ($favoriteArtworks as $artwork): ?>
            <?php
            $buttonText = 'Aus Favoriten entfernen: ' . $artwork->getTitle();
            $buttonHref = 'remove-favorite.php?type=artwork&id=' . urlencode((string) $artwork->getArtworkid());
            $buttonVariant = 'danger';
            include __DIR__ . '/../components/button.php';
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h2>Favorisierte Künstler</h2>
<?php if(empty($favoriteArtistIds)) : ?>
    <?php
    $alertType="info";
    $alertMessage="Du hast noch keine Künstler favorisiert.";
    include __DIR__ . '/../components/alert-box.php';
    ?>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($favoriteArtists as $artist): ?>
            <?php include __DIR__ . '/../components/artist-card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="mt-3">
        <?php foreach ($favoriteArtists as $artist): ?>
            <?php
            $artistName = $artist->getFirstName() . ' ' . $artist->getLastName();

            $buttonText = 'Aus Favoriten entfernen: ' . $artistName;
            $buttonHref = 'remove-favorite.php?type=artist&id=' . urlencode((string) $artist->getId());
            $buttonVariant = 'danger';
            include __DIR__ . '/../components/button.php';
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
