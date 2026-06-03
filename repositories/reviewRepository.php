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

    public function getAverageRatingArtwork($artworkId)
    {
        $sql = "SELECT AVG(Rating) as AvgRating, COUNT(ReviewId) as TotalReviews 
                FROM reviews 
                WHERE ArtWorkId = :id";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $artworkId]);

        return $stmt->fetch();
    }

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

    public function deleteReview($reviewId)
    {
        $sql = "DELETE FROM reviews WHERE ReviewId = :reviewId";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute(['reviewId' => $reviewId]);
    }

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





