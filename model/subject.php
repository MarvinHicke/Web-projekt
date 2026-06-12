<?php

/**
 * Repräsentiert ein Thema (Subject) eines Kunstwerks aus der Datenbank
 */
class subject
{
    private $subjectId;
    private $subjectName;

    /**
     * Erstellt ein Subject-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->subjectId = $data['SubjectId'];
        $this->subjectName = $data['SubjectName'];
    }

    // Getter

    /**
     * Gibt die ID des Themas zurück
     *
     * @return int Die Thema-ID
     */
    public function getSubjectid()
    {
        return $this->subjectId;
    }

    /**
     * Gibt den Namen des Themas zurück
     *
     * @return string Der Name des Themas
     */
    public function getSubjectname()
    {
        return $this->subjectName;
    }
}