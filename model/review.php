<?php
class review
{
    public $id;
    public $artworkId;
    public $rating;
    public $comment;
    public $reviewDate;
    public $customerId;

    public function __construct($row)
    {
        $this->id = $row['ReviewId'] ?? null;
        $this->artworkId = $row['ArtWorkId'] ?? null;
        $this->customerId =$row['CustomerId'] ?? null;
        $this->rating = $row['Rating'] ?? 0;
        $this->comment = $row['Comment'] ?? '';
        $this->reviewDate = $row['ReviewDate'] ?? '';
    }

    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    public function getArtworkId()
    {
        return $this->artworkId;
    }

    function getCustomerId()
    {
        return $this->customerId;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getReviewId()
    {
        return $this->id;
    }

    public function getReviewDateFormatted()
    {
        return date('d.m.Y', strtotime($this->reviewDate));
    }
}