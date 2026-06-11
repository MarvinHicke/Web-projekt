<?php
require_once __DIR__ . '/../helpers.php';

/**
 * Renders the Most Recent Reviews box on the homepage.
 * Expects $reviews as raw associative arrays containing:
 *   ReviewId, ArtWorkId, Rating, Comment, ReviewDate, ArtworkTitle
 * Use reviewRepository->getLatestReviewsWithDetails($limit) to get this data.
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
                    $artworkId    = (int)    ($review['ArtWorkId']    ?? 0);
                    $artworkTitle = (string) ($review['ArtworkTitle'] ?? 'Unbekanntes Kunstwerk');
                    $comment      = cleanHtml((string) ($review['Comment'] ?? ''));
                    $rating       = (string) ($review['Rating']       ?? '');
                    $date         = (string) ($review['ReviewDate']   ?? '');
                    $dateFormatted = $date ? date('d.m.Y', strtotime($date)) : '';
                    ?>
                    <li>
                        <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                            <strong><?= e($artworkTitle); ?></strong>
                            <?php if ($comment !== ''): ?>
                                <span><?= e(mb_strimwidth($comment, 0, 80, '…')); ?></span>
                            <?php endif; ?>
                            <small><?= e($rating); ?>/5 · <?= e($dateFormatted); ?></small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
