<?php


require_once __DIR__ . "/../model/artist.php";


class artistRepository {
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM artists";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];

        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }

        return $artists;
    }
}