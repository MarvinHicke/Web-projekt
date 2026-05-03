<?php


require_once "./../model/artist.php";


class artistRepository {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists ORDER BY FirstName " . $sortOrder;

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
}

