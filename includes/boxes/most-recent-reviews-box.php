<?php
/**
 * Box „Neueste Bewertungen" für die Startseite (UC02).
 *
 * Zeigt die zuletzt abgegebenen Bewertungen mit Kunstwerkname,
 * Kurztext (max. 80 Zeichen), Bewertung und Datum.
 * Jeder Eintrag verlinkt auf die zugehörige Kunstwerk-Einzelansicht.
 *
 * Erwartet ein Array aus assoziativen Arrays mit den Schlüsseln:
 *   ReviewId, ArtWorkId, Rating, Comment, ReviewDate, ArtworkTitle
 *
 * Datenquelle: reviewRepository->getLatestReviewsWithDetails($limit)
 * (eigene Methode mit JOIN auf artworks, gibt rohe Arrays zurück)
 */

require_once __DIR__ . '/../helpers.php';

/**
 * Rendert die Neueste-Bewertungen-Box auf der Startseite.
 *
 * @param array $reviews Array aus assoziativen Arrays der neuesten Bewertungen
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
                    $artworkId     = (int)    ($review['ArtWorkId']    ?? 0);
                    $artworkTitle  = (string) ($review['ArtworkTitle'] ?? 'Unbekanntes Kunstwerk');

                    // HTML-Tags aus DB-Inhalten entfernen (cleanHtml aus helpers.php)
                    $comment       = cleanHtml((string) ($review['Comment']    ?? ''));
                    $rating        = (string) ($review['Rating']               ?? '');
                    $date          = (string) ($review['ReviewDate']           ?? '');

                    // Datum in deutsches Format umwandeln (TT.MM.JJJJ)
                    $dateFormatted = $date ? date('d.m.Y', strtotime($date)) : '';
                    ?>
                    <li>
                        <!-- Jede Bewertung verlinkt auf die Kunstwerk-Einzelansicht (UC02) -->
                        <a href="<?= e(artworkDetailUrl($artworkId)); ?>">
                            <strong><?= e($artworkTitle); ?></strong>
                            <?php if ($comment !== ''): ?>
                                <!-- Kommentar auf 80 Zeichen kürzen -->
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
