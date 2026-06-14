<?php
/**
 * Box „Top Werke" für die Startseite (UC02).
 *
 * Zeigt die am besten bewerteten Kunstwerke mit Sternbewertung.
 * Erwartet ein Array aus assoziativen Arrays mit den Schlüsseln:
 *   ArtWorkID, Title, FirstName, LastName, AvgRating
 *
 * Datenquelle: artworkRepository->getTopArtworks($limit)
 * (nur Kunstwerke mit mindestens 3 Bewertungen werden berücksichtigt)
 */

require_once __DIR__ . '/../helpers.php';

/**
 * Rendert die Top-Werke-Box auf der Startseite.
 *
 * @param array $artworks Array aus assoziativen Arrays der Top-Kunstwerke
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

                    // Vollständigen Künstlernamen zusammensetzen
                    $artistName = trim($firstName . ' ' . $lastName);
                    $rating     = $work['AvgRating'] ?? null;
                    ?>
                    <li>
                        <a href="<?= e(artworkDetailUrl($id)); ?>">
                            <strong><?= e($title); ?></strong>
                            <span><?= e($artistName !== '' ? $artistName : 'Unbekannter Künstler'); ?></span>

                            <?php if ($rating !== null): ?>
                                <?php
                                // Bewertungsprozent für die CSS-Sternfüllung berechnen (0–100 %)
                                $ratingPercent = max(0, min(100, ((float) $rating / 5) * 100));
                                ?>
                                <!-- Sterndarstellung über CSS-Variable (UC02: Sterne sichtbar) -->
                                <span
                                    class="rating-stars"
                                    style="--rating-percent: <?= e(number_format($ratingPercent, 2, '.', '')); ?>%;"
                                    aria-label="<?= e(number_format((float) $rating, 1, ',', '.')); ?> von 5 Sternen"
                                >
                                    <span class="rating-stars-empty" aria-hidden="true">★★★★★</span>
                                    <span class="rating-stars-fill"  aria-hidden="true">★★★★★</span>
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
