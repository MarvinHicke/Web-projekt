<?php
require_once __DIR__ . '/../helpers.php';

/**
 * Renders the Most Recent Reviews box on the homepage.
 * Needed data later:
 * review date, user name, rating, text, artwork id, artwork title.
 * Link:
 * every review links to the reviewed artwork.
 */
function renderMostRecentReviewsBox(array $reviews): void
{
    usort($reviews, static function (array $a, array $b): int {
        return strcmp((string) $b['date'], (string) $a['date']);
    });

    $recentReviews = array_slice($reviews, 0, 3);
    ?>
    <section class="data-box">
        <h2>Most-recent reviews</h2>
        <p class="box-note">Newest community opinions.</p>

        <ul class="clean-list">
            <?php foreach ($recentReviews as $review): ?>
                <li>
                    <a href="<?= e(artworkUrl((int) $review['artwork_id'])); ?>">
                        <strong><?= e((string) $review['artwork_title']); ?></strong>
                        <span><?= e((string) $review['text']); ?></span>
                        <small>
                            <?= e((string) $review['user']); ?> · <?= e((string) $review['rating']); ?>/5
                        </small>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php
}
