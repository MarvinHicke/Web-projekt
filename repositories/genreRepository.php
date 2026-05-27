<?php

require_once __DIR__ . '/../model/genre.php';

class genreRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM genres";

        $stmt = $this->db->preparedstatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $genres = [];

        foreach ($rows as $row)
        {
            $genres[] = new genre($row);
        }

        return $genres;
    }

    public function getAllForBrowse()
    {
        $sql = "SELECT * FROM genres ORDER BY Era ASC, GenreName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGenresForArtwork($artworkId)
    {
        $sql = "SELECT g.* FROM genres g, ArtworkGenres ag 
            WHERE g.GenreID = ag.GenreID AND ag.ArtWorkID = :artworkId
            ORDER BY g.GenreName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':artworkId', (int)$artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $genres = [];
        foreach ($rows as $row)
        {
            $genres[] = new genre($row);
        }
        return $genres;
    }
}



