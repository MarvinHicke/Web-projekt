<?php

require_once __DIR__ . "/../model/subject.php";


class subjectRepository
{
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

    public function getById($id)
    {
        $sql = "SELECT * FROM subjects WHERE subjectid = :id";

        $stmt = $this->db->preparedStatement($sql);

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Subject($row) : null;
    }

    public function getAllForBrowse()
    {
        $sql = "SELECT * FROM subjects ORDER BY SubjectName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectsForArtwork($artworkId)
    {
        $sql = "SELECT s.* FROM subjects s, ArtworkSubjects asub 
            WHERE s.SubjectID = asub.SubjectID AND asub.ArtWorkID = :artworkId
            ORDER BY s.SubjectName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':artworkId', (int)$artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $subjects = [];
        foreach ($rows as $row)
        {
            $subjects[] = new subject($row);
        }
        return $subjects;
    }
}
