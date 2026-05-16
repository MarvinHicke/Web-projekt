<?php

require_once __DIR__ . "/../model/subject.php";


class subjectRepository {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM subjects";

        $stmt = $this->db->preparedstatement($sql);
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
