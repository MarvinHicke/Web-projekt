<?php

require_once __DIR__ . "/../model/artist.php";

class artistRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists ORDER BY LastName " . $sortOrder . ", FirstName " . $sortOrder;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];

        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }

        return $artists;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM artists WHERE ArtistID = :id";

        $stmt = $this->db->preparedStatement($sql);

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Artist($row) : null;
    }

    public function getMostReviewedArtists($limit = 3)
    {
        $sql =
            "SELECT a.*, 
               (SELECT COUNT(r.ReviewId) 
                FROM reviews r, artworks aw 
                WHERE r.ArtWorkId = aw.ArtWorkID AND aw.ArtistID = a.ArtistID) as ReviewCount
            FROM artists a
            ORDER BY ReviewCount DESC
            LIMIT :limit";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $artists = [];
        while ($row = $stmt->fetch())
        {
            $artists[] = $row;
        }
        return $artists;
    }

    public function getAllSorted($direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists ORDER BY LastName " . $dir . ", FirstName " . $dir;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchByLastName($keyword)
    {
        $searchString = $keyword . '%';

        $sql = "SELECT * FROM artists WHERE LastName LIKE :keyword ORDER BY LastName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['keyword' => $searchString]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

