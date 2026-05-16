<?php

require_once __DIR__ . "/../model/gallery.php";

class galleryRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM galleries";

        $stmt = $this->db->preparedstatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $galleries= [];

        foreach ($rows as $row) {
            $galleries[] = new gallery($row);
        }

        return $galleries;
    }
}

