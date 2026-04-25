<?php
require_once __DIR__ . '/../helpers.php';

/**
 * Renders the Most Reviewed Artists box on the homepage.
 * Needed data later:
 * artist id, first name, last name, review count, image optional.
 * Link:
 * every item links to artist.php?id=ARTIST_ID
 */
function renderMostReviewedArtistsBox(array $artists): void
{
    usort($artists, static function (array $a, array $b): int {
        return ($b['review_count'] <=> $a['review_count']);
    });

    $topArtists = array_slice($artists, 0, 3);
    ?>
    <section class="data-box">
        <h2>Most-reviewed artists</h2>
        <p class="box-note">Artists with many reviews.</p>

        <ul class="clean-list">
            <?php foreach ($topArtists as $artist): ?>
                <li>
                    <a href="<?= e(artistUrl((int) $artist['id'])); ?>">
                        <strong>
                            <?= e((string) $artist['first_name'] . ' ' . (string) $artist['last_name']); ?>
                        </strong>
                        <small><?= e((string) $artist['review_count']); ?> reviews</small>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php
}
