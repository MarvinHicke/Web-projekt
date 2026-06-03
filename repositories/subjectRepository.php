<?php

require_once __DIR__ . "/../model/subject.php";

/**
 * Repository für Datenbankabfragen rund um die Themen (Subjects)
 */
class subjectRepository
{
    private $db;

    /**
     * Erstellt eine neue Instanz des subjectRepository
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Holt alle Themen aus der Datenbank
     *
     * @return array Ein Array aus fertigen subject-Objekten
     */
    public function findAll()
    {
        $sql = "SELECT * FROM subjects";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $subjects = [];

        foreach ($rows as $row)
        {
            $subjects[] = new subject($row);
        }

        return $subjects;
    }

    /**
     * Sucht ein bestimmtes Thema anhand seiner ID
     *
     * @param int $id Die ID des Themas
     * @return subject Das gefundene subject-Objekt oder null falls nichts existiert
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM subjects WHERE SubjectId = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new subject($row) : null;
    }

    /**
     * Holt alle Themen sortiert nach Name für die Übersicht
     *
     * @return array Ein Array aus fertigen subject-Objekten
     */
    public function getAllForBrowse()
    {
        $sql = "SELECT * FROM subjects ORDER BY SubjectName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Holt alle Themen die einem bestimmten Kunstwerk zugeordnet sind
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @return array Ein Array aus fertigen subject-Objekten
     */
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
