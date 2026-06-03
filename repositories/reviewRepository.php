<?php
require_once __DIR__ . '/../model/review.php';

/**
 * Repository für Datenbankabfragen rund um die Bewertungen (Reviews)
 */
class reviewRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Sucht eine bestimmte Bewertung anhand ihrer ID
     *
     * @param int $id Die ID der Bewertung
     * @return review Das gefundene review-Objekt oder null falls nichts existiert
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM reviews WHERE ReviewId = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new review($row) : null;
    }

    /**
     * Erstellt eine neue Instanz des reviewRepository
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function getForArtwork($artworkId)
    {
        $sql = "SELECT * FROM reviews WHERE ArtWorkId = :id ORDER BY ReviewDate DESC";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $artworkId]);

        $reviews = [];
        while ($row = $stmt->fetch())
        {
            $reviews[] = new review($row);
        }
        return $reviews;
    }

    /**
     * Holt alle Bewertungen für ein bestimmtes Kunstwerk sortiert nach Datum
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @return array Ein Array aus fertigen review-Objekten
     */
    public function getLatestReviews($limit = 3)
    {
        $sql = "SELECT * FROM reviews ORDER BY ReviewDate DESC LIMIT :limit";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $reviews = [];
        while ($row = $stmt->fetch())
        {
            $reviews[] = new review($row);
        }
        return $reviews;
    }

    /**
     * Holt die neuesten Bewertungen plattformweit mit einem Limiter
     *
     * @param int $limit Die maximale Anzahl der Bewertungen (Standard ist 3)
     * @return array Ein Array aus fertigen review-Objekten
     */
    public function getAverageRatingArtwork($artworkId)
    {
        $sql = "SELECT AVG(Rating) as AvgRating, COUNT(ReviewId) as TotalReviews 
                FROM reviews 
                WHERE ArtWorkId = :id";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $artworkId]);

        return $stmt->fetch();
    }

    /**
     * Speichert eine neue Bewertung in der Datenbank
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @param int $customerId Die ID des Kunden
     * @param int $rating Die Sterne-Bewertung
     * @param string $comment Der Bewertungstext
     * @return bool True bei Erfolg andernfalls false
     */
    public function addReview($artworkId, $customerId, $rating, $comment)
    {
        $currentDate = date('d.m.Y H:i:s');
        $sql = "INSERT INTO reviews (ArtWorkId, CustomerId, ReviewDate, Rating, Comment) 
                VALUES (:artworkId, :customerId, :reviewDate, :rating, :comment)";

        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute
        ([
            'artworkId' => $artworkId,
            'customerId' => $customerId,
            'reviewDate' => $currentDate,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    /**
     * Löscht eine Bewertung anhand ihrer ID
     *
     * @param int $reviewId Die ID der Bewertung
     * @return bool True bei Erfolg andernfalls false
     */
    public function deleteReview($reviewId)
    {
        $sql = "DELETE FROM reviews WHERE ReviewId = :reviewId";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute(['reviewId' => $reviewId]);
    }

    /**
     * Prüft ob ein bestimmter Kunde ein Kunstwerk bereits bewertet hat
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @param int $customerId Die ID des Kunden
     * @return bool True wenn bereits eine Bewertung existiert andernfalls false
     */
    public function hasUserReviewedArtwork($artworkId, $customerId)
    {
        $sql = "SELECT COUNT(*) as Count FROM reviews WHERE ArtWorkId = :artworkId AND CustomerId = :customerId";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute
        ([
            'artworkId' => $artworkId,
            'customerId' => $customerId
        ]);

        return $stmt->fetch()['Count'] > 0;
    }

}





