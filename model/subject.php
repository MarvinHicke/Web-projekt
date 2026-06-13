<?php

/**
 * Repräsentiert ein Thema (Subject) eines Kunstwerks aus der Datenbank
 */
class subject
{
    private $subjectId;
    private $subjectName;
    private $imageFilename;

    /**
     * Erstellt ein Subject-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->subjectId = $data['SubjectId'];
        $this->subjectName = $data['SubjectName'];
        $this->imageFilename =
            $data['ImageFileName']
            ?? $data['ImageFilename']
            ?? $data['imageFilename']
            ?? $data['imagefilename']
            ?? $data['ArtistID']
            ?? null;    }

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

    public function getImagefilename()
    {
        $name = $this->imageFilename;

        if ($name === null || $name === '') {
            return null;
        }

        if (is_numeric($name) && strlen((string) $name) < 6) {
            $name = str_pad((string) $name, 6, '0', STR_PAD_LEFT);
        }

        return $name;
    }
}