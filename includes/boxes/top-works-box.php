<?php
require_once __DIR__ . '/../helpers.php';

/**
 * Renders the Top Works box on the homepage.
 * Expects $artworks as raw associative arrays containing:
 *   ArtWorkID, Title, FirstName, LastName, AvgRating
 * Use artworkRepository->getTopArtworks($limit) after updating its SQL to JOIN artists.
 */
function renderTopWorksBox(array $artworks): void
{
    ?>
    <section class="data-box">
        <h2>Top Werke</h2>
        <p class="box-note">Am besten bewertete Kunstwerke.</p>

        <?php if (empty($artworks)): ?>
            <p>Keine Top Werke gefunden.</p>
        <?php else: ?>
            <ul class="clean-list">
                <?php foreach ($artworks as $work): ?>
                    <?php
                    $id         = (int)    ($work['ArtWorkID'] ?? 0);
                    $title      = (string) ($work['Title']     ?? 'Unbekanntes Kunstwerk');
                    $firstName  = (string) ($work['FirstName'] ?? '');
                    $lastName   = (string) ($work['LastName']  ?? '');
                    $artistName = trim($firstName . ' ' . $lastName);
                    $rating     = $work['AvgRating'] ?? null;
                    ?>
                    <li>
                        <a href="<?= e(artworkDetailUrl($id)); ?>">
                            <strong><?= e($title); ?></strong>
                            <span><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></span>
                            <?php if ($rating !== null): ?>
                                <?php $ratingPercent = max(0, min(100, ((float) $rating / 5) * 100)); ?>
                                <span
                                    class="rating-stars"
                                    style="--rating-percent: <?= e(number_format($ratingPercent, 2, '.', '')); ?>%;"
                                    aria-label="<?= e(number_format((float) $rating, 1, ',', '.')); ?> von 5 Sternen"
                                >
                                    <span class="rating-stars-empty" aria-hidden="true">★★★★★</span>
                                    <span class="rating-stars-fill" aria-hidden="true">★★★★★</span>
                                </span>
                                <small><?= e(number_format((float) $rating, 1, ',', '.')); ?>/5</small>
                            <?php else: ?>
                                <small>Noch keine Bewertung</small>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
