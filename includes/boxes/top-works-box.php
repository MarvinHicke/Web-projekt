<?php
require_once __DIR__ . '/../helpers.php';

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
                    $id = (int) ($work['ArtWorkID'] ?? $work['id'] ?? 0);
                    $title = (string) ($work['Title'] ?? $work['title'] ?? 'Unbekanntes Kunstwerk');
                    $artistName = trim((string) ($work['FirstName'] ?? $work['artist_first_name'] ?? '') . ' ' . (string) ($work['LastName'] ?? $work['artist_last_name'] ?? ''));
                    $rating = $work['AvgRating'] ?? $work['average_rating'] ?? null;
                    ?>
                    <li>
                        <a href="<?= e(artworkDetailUrl($id)); ?>">
                            <strong><?= e($title); ?></strong>
                            <span><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></span>
                            <small>
                                Bewertung <?= $rating !== null ? e(number_format((float) $rating, 1, ',', '.')) . '/5' : 'noch nicht vorhanden'; ?>
                            </small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
