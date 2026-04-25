<?php

require_once "./../model/gallery.php";

class galleryRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM galleries";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $galleries= [];

        foreach ($rows as $row) {
            $galleries[] = new gallery($row);
        }

        return $galleries;
    }
}

