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
     * Holt alle Bewertungen für ein Kunstwerk inklusive Stadt und Land des Reviewers.
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @return array Bewertungen als assoziative Arrays
     */
    public function getForArtworkWithCustomerData($artworkId)
    {
        $sql = "SELECT r.ReviewId,
                       r.ArtWorkId,
                       r.CustomerId,
                       r.Rating,
                       r.Comment,
                       r.ReviewDate,
                       c.FirstName AS ReviewerFirstName,
                       c.LastName AS ReviewerLastName,
                       c.City,
                       c.Country
                FROM reviews r
                LEFT JOIN customers c ON r.CustomerId = c.CustomerID
                WHERE r.ArtWorkId = :id
                ORDER BY r.ReviewDate DESC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute
        ([
            'id' => (int) $artworkId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $currentDate = date('Y-m-d H:i:s');
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

    /**
     * Holt die neuesten Bewertungen mit Artwork-Titel für die Startseiten-Box.
     * Gibt rohe Arrays zurück (kein review-Objekt), damit der Box-Renderer
     * direkt auf ArtWorkId, ArtworkTitle, Rating, Comment, ReviewDate zugreifen kann.
     *
     * @param int $limit Maximale Anzahl Bewertungen (Standard 3)
     * @return array Array von assoziativen Arrays
     */
    public function getLatestReviewsWithDetails($limit = 3)
    {
        $sql = "SELECT r.ReviewId, r.ArtWorkId, r.Rating, r.Comment, r.ReviewDate,
                       a.Title AS ArtworkTitle
                FROM reviews r
                JOIN artworks a ON r.ArtWorkId = a.ArtWorkID
                ORDER BY r.ReviewDate DESC
                LIMIT :limit";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Holt alle Bewertungen eines Kunden inklusive Kunstwerk- und Künstlerdaten.
     *
     * @param int $customerId Die ID des Kunden
     * @return array Bewertungen als assoziative Arrays
     */
    public function getForCustomerWithArtworkData($customerId)
    {
        $sql = "SELECT r.ReviewId,
                   r.ArtWorkId,
                   r.CustomerId,
                   r.ReviewDate,
                   r.Rating,
                   r.Comment,
                   a.Title AS ArtworkTitle,
                   art.FirstName AS ArtistFirstName,
                   art.LastName AS ArtistLastName
                FROM reviews r
                JOIN artworks a ON r.ArtWorkId = a.ArtWorkID
                JOIN artists art ON a.ArtistID = art.ArtistID
                WHERE r.CustomerId = :customerId
                ORDER BY r.ReviewDate DESC, r.ReviewId DESC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute
        ([
            'customerId' => (int) $customerId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
