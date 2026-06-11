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
                            <small>
                                Bewertung <?= $rating !== null
                                    ? e(number_format((float) $rating, 1, ',', '.')) . '/5'
                                    : 'noch nicht vorhanden'; ?>
                            </small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
