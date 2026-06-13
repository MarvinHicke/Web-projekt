<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/dbaccess.php';
require_once __DIR__ . '/../repositories/artistRepository.php';
require_once __DIR__ . '/../repositories/genreRepository.php';

$pageTitle = 'Erweiterte Suche';

try
{
    $db = new dbaccess();
    $db->connect();

    $artistRepo = new artistRepository($db);
    $genreRepo  = new genreRepository($db);

    $nationalities = $artistRepo->getNationalities();
    $genres = $genreRepo->getAllForBrowse();

    $db->close();
} catch (Exception $e)
{
    $nationalities = [];
    $genres = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

    <div class="container mt-3">
        <h1>Erweiterte Suche</h1>
        <p>Hier kannst du genauer nach Künstlern oder Kunstwerken filtern.</p>

        <hr>

        <h3>Künstler suchen</h3>
        <form method="get" action="search-results.php">

            <input type="hidden" name="search_type" value="artist">
            <input type="hidden" name="sort" value="artist">
            <input type="hidden" name="dir" value="ASC">

            <label>Name:</label>
            <input type="text" class="form-control" name="artist_name" placeholder="z. B. Vincent">
            <br>

            <label>Nationalität:</label>
            <select class="form-control" name="nationality">
                <option value="">-- Alle --</option>
                <?php foreach ($nationalities as $nat): ?>
                    <option value="<?php echo htmlspecialchars($nat); ?>">
                        <?php echo htmlspecialchars($nat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <label>Gelebt von:</label>
            <div class="row">
                <div class="col">
                    <input type="number" class="form-control" name="year_min" placeholder="von Jahr">
                </div>
                <div class="col">
                    <input type="number" class="form-control" name="year_max" placeholder="bis Jahr">
                </div>
            </div>
            <br>

            <button type="submit" class="btn btn-primary">Künstler filtern</button>
        </form>

        <hr class="my-5">

        <h3>Kunstwerke suchen</h3>
        <form method="get" action="search-results.php">

            <input type="hidden" name="search_type" value="artwork">
            <input type="hidden" name="sort" value="title">
            <input type="hidden" name="dir" value="ASC">

            <label>Titel des Kunstwerks:</label>
            <input type="text" class="form-control" name="artwork_title" placeholder="z. B. Mona">
            <br>

            <label>Genre:</label>
            <select class="form-control" name="genre">
                <option value="">-- Alle --</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['GenreID']); ?>">
                        <?php echo htmlspecialchars($g['GenreName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <label>Entstehungsjahr:</label>
            <div class="row">
                <div class="col">
                    <input type="number" class="form-control" name="year_min" placeholder="von Jahr">
                </div>
                <div class="col">
                    <input type="number" class="form-control" name="year_max" placeholder="bis Jahr">
                </div>
            </div>
            <br>

            <button type="submit" class="btn btn-success">Kunstwerke filtern</button>
        </form>
    </div>

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
