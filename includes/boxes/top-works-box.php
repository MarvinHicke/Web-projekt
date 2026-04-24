<?php
require_once __DIR__ . '/../helpers.php';

/**
 * Renders the Top Works box on the homepage.
 * Needed data later:
 * artwork id, title, artist name, average rating, image.
 * Link:
 * every item links to artwork.php?id=ARTWORK_ID
 */
function renderTopWorksBox(array $artworks): void
{
    usort($artworks, static function (array $a, array $b): int {
        return ($b['average_rating'] <=> $a['average_rating']);
    });

    $topWorks = array_slice($artworks, 0, 3);
    ?>
    <section class="data-box">
        <h2>Top works</h2>
        <p class="box-note">Best rated artworks.</p>

        <ul class="clean-list">
            <?php foreach ($topWorks as $work): ?>
                <li>
                    <a href="<?= e(artworkUrl((int) $work['id'])); ?>">
                        <strong><?= e((string) $work['title']); ?></strong>
                        <span>
                            <?= e((string) $work['artist_first_name'] . ' ' . (string) $work['artist_last_name']); ?>
                        </span>
                        <small>Rating <?= e((string) $work['average_rating']); ?>/5</small>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php
}
