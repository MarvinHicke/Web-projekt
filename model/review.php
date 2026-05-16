<?php
class review
{
    public $id;
    public $artworkId;
    public $rating;
    public $comment;
    public $reviewDate;

    public function __construct($row)
    {
        $this->id = $row['ReviewId'] ?? null;
        $this->artworkId = $row['ArtworkId'] ?? null;
        $this->rating = $row['Rating'] ?? 0;
        $this->comment = $row['Comment'] ?? '';
        $this->reviewDate = $row['ReviewDate'] ?? '';
    }
}

