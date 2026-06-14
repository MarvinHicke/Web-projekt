<?php
/**
 * Box „Meistbewertete Künstler" für die Startseite (UC02).
 *
 * Zeigt die Künstler mit den meisten Bewertungen und deren Anzahl.
 * Erwartet ein Array aus assoziativen Arrays mit den Schlüsseln:
 *   ArtistID, FirstName, LastName, ReviewCount
 *
 * Datenquelle: artistRepository->getMostReviewedArtists($limit)
 */

require_once __DIR__ . '/../helpers.php';

/**
 * Rendert die Meistbewerteten-Künstler-Box auf der Startseite.
 *
 * @param array $artists Array aus assoziativen Arrays der meistbewerteten Künstler
 */
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
                    // Unterstützt sowohl DB-Schlüssel (ArtistID) als auch Mock-Schlüssel (id)
                    $id          = (int) ($artist['ArtistID'] ?? $artist['id'] ?? 0);
                    $artistName  = trim(
                        (string) ($artist['FirstName'] ?? $artist['first_name'] ?? '') . ' ' .
                        (string) ($artist['LastName']  ?? $artist['last_name']  ?? '')
                    );
                    $reviewCount = (int) ($artist['ReviewCount'] ?? $artist['review_count'] ?? 0);
                    ?>
                    <li>
                        <!-- Link zur Künstler-Einzelansicht (UC11) -->
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
