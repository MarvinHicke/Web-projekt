<?php

require_once "./../model/subject.php";


class subjectRepository {
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM subjects";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $subjects = [];

        foreach ($rows as $row)
        {
            $subjects[] = new subject($row);
        }

        return $subjects;
    }
}
