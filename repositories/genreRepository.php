<?php

require_once "./../model/genre.php";


class genreRepository {
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM genres";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $genres = [];

        foreach ($rows as $row)
        {
            $genres[] = new genre($row);
        }

        return $genres;
    }
}



