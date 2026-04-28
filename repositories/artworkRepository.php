<?php

require_once "./../model/artwork.php";


class artworkRepository {
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM artworks";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }
}

?>