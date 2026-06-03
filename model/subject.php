<?php

/**
 * Repräsentiert ein Thema (Subject) eines Kunstwerks aus der Datenbank
 */
class subject
{
    private $subjectid;
    private $subjectname;

    /**
     * Erstellt ein Subject-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->subjectid = $data['SubjectId'];
        $this->subjectname = $data['SubjectName'];
    }

    // Getter

    /**
     * Gibt die ID des Themas zurück
     *
     * @return int Die Thema-ID
     */
    public function getSubjectid()
    {
        return $this->subjectid;
    }

    /**
     * Gibt den Namen des Themas zurück
     *
     * @return string Der Name des Themas
     */
    public function getSubjectname()
    {
        return $this->subjectname;
    }
}