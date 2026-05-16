<?php

require_once __DIR__ . '/../model/genre.php';

class genreRepository {
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
}



