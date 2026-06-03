<?php
require_once __DIR__ . '/../helpers.php';

function renderMostReviewedArtistsBox(array $artists): void
{
    ?>
    <section class="data-box">
        <h2>Meistbewertete Künstler</h2>
        <p class="box-note">Künstler mit vielen Bewertungen.</p>

        <?php if (empty($artists)): ?>
            <p>Keine Künstler gefunden.</p>
        <?php else: ?>
            <ul class="clean-list">
                <?php foreach ($artists as $artist): ?>
                    <?php
                    $id = (int) ($artist['ArtistID'] ?? $artist['id'] ?? 0);
                    $artistName = trim((string) ($artist['FirstName'] ?? $artist['first_name'] ?? '') . ' ' . (string) ($artist['LastName'] ?? $artist['last_name'] ?? ''));
                    $reviewCount = (int) ($artist['ReviewCount'] ?? $artist['review_count'] ?? 0);
                    ?>
                    <li>
                        <a href="<?= e(artistDetailUrl($id)); ?>">
                            <strong><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></strong>
                            <small><?= e((string) $reviewCount); ?> Bewertungen</small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php
}
