<?php

require_once "./../model/artwork.php";

class artworkRepository{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artworks ORDER BY Title " . $sortOrder;

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
}
