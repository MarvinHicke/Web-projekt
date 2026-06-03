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
    ?>
    <section class="data-box">
        <h2>Neueste Bewertungen</h2>
        <p class="box-note">Aktuelle Bewertungen aus der Datenbank.</p>

        <?php if (empty($reviews)): ?>
            <p>Keine Bewertungen gefunden.</p>
        <?php else: ?>
            <ul class="clean-list">
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $artworkId = (int) ($review['ArtWorkId'] ?? $review['artwork_id'] ?? 0);
                    $title = (string) ($review['Title'] ?? $review['artwork_title'] ?? 'Unbekanntes Kunstwerk');
                    $comment = (string) ($review['Comment'] ?? $review['text'] ?? '');
                    $rating = (string) ($review['Rating'] ?? $review['rating'] ?? '');
                    $date = (string) ($review['ReviewDate'] ?? $review['date'] ?? '');
                    ?>
                    <li>
                        <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                            <strong><?= e($title); ?></strong>
                            <span><?= e($comment !== '' ? $comment : 'Keine Beschreibung'); ?></span>
                            <small><?= e($rating); ?>/5 · <?= e($date); ?></small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
