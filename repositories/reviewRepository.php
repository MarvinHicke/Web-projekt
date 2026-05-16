<?php
require_once __DIR__ . '/../model/review.php';

class reviewRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getForArtwork($artworkId)
    {
        $sql = "SELECT * FROM ArtworkReviews WHERE ArtworkId = :id ORDER BY ReviewDate DESC";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $artworkId]);

        $reviews = [];
        while ($row = $stmt->fetch())
        {
            $reviews[] = new review($row);
        }
        return $reviews;
    }

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
}

