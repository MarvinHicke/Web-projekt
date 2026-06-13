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

<section class="page-heading">
    <p class="eyebrow">Ihre persönliche Auswahl</p>
    <h1>Favoriten</h1>
    <p class="mb-0">Hier sehen Sie Ihre favorisierten Künstler und Kunstwerke.</p>
</section>

<section class="favorites-section">
<div class="section-heading">
    <h2>Favorisierte Kunstwerke</h2>
    <span class="badge rounded-pill text-bg-light"><?= count($favoriteArtworks); ?></span>
</div>
<?php if(empty($favoriteArtworks)) : ?>
    <?php
    $alertType="info";
    $alertMessage="Du hast noch keine Kunstwerke favorisiert.";
    include __DIR__ . '/../components/alert-box.php';
    ?>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($favoriteArtworks as $artwork): ?>
            <?php
            $showRemoveFavoriteButton = true;
            include __DIR__ . '/../components/artwork-card.php';
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</section>

<section class="favorites-section">
<div class="section-heading">
    <h2>Favorisierte Künstler</h2>
    <span class="badge rounded-pill text-bg-light"><?= count($favoriteArtists); ?></span>
</div>
<?php if(empty($favoriteArtists)) : ?>
    <?php
    $alertType="info";
    $alertMessage="Du hast noch keine Künstler favorisiert.";
    include __DIR__ . '/../components/alert-box.php';
    ?>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($favoriteArtists as $artist): ?>
            <?php
            $showRemoveFavoriteButton = true;
            include __DIR__ . '/../components/artist-card.php';
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
