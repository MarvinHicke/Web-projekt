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

        foreach ($rows as $row)
        {
            $galleries[] = new gallery($row);
        }

        return $galleries;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM galleries WHERE GalleryID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new gallery($row) : null;
    }

}

