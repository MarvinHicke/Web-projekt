<?php

require_once __DIR__ . "/../model/artwork.php";

class artworkRepository
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT a.*, art.FirstName, art.LastName 
        FROM artworks a, artists art
        WHERE a.ArtistID = art.ArtistID
        ORDER BY a.Title " . $sortOrder;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM artworks WHERE ArtWorkID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new artwork($row) : null;
    }

    public function getTopArtworks($limit = 3)
    {
        $sql = "SELECT a.*, 
                   (SELECT IF(COUNT(r.ReviewId) >= 3, AVG(r.Rating), NULL) 
                    FROM reviews r 
                    WHERE r.ArtWorkId = a.ArtWorkID) as AvgRating
            FROM artworks a
            ORDER BY AvgRating DESC
            LIMIT :limit";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $artworks = [];
        while ($row = $stmt->fetch())
        {
            $artworks[] = $row;
        }
        return $artworks;
    }

    public function getAllSorted($sortBy = 'title', $direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        switch (strtolower($sortBy)) {
            case 'year':
                $orderClause = "a.YearOfWork $dir";
                break;
            case 'artist':
                $orderClause = "art.LastName $dir, art.FirstName $dir";
                break;
            case 'title':
            default:
                $orderClause = "a.Title $dir";
                break;
        }

        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a
            JOIN artists art ON a.ArtistID = art.ArtistID
            ORDER BY $orderClause";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForArtist($artistId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
        FROM artworks a, artists art
        WHERE a.ArtistID = art.ArtistID AND a.ArtistID = :artistId
        ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':artistId', (int)$artistId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    public function getForGenre($genreId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, ArtworkGenres ag, artists art
            WHERE a.ArtWorkID = ag.ArtWorkID 
              AND a.ArtistID = art.ArtistID 
              AND ag.GenreID = :genreId
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':genreId', (int)$genreId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }
        return $artworks;
    }

    public function getForSubject($subjectId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, ArtworkSubjects xas, artists art
            WHERE a.ArtWorkID = xas.ArtWorkID 
              AND a.ArtistID = art.ArtistID 
              AND xas.SubjectID = :subjectId
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':subjectId', (int)$subjectId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }
        return $artworks;
    }

    public function searchByTitle($keyword)
    {
        $searchString = $keyword . '%';

        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, artists art 
            WHERE a.ArtistID = art.ArtistID AND a.Title LIKE :keyword 
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['keyword' => $searchString]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

