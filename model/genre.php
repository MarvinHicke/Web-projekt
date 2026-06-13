<?php

/**
 * Repräsentiert ein Kunst-Genre mit seinen Details aus der Datenbank
 */
class genre
{
    private $genreId;
    private $genreName;
    private $era;
    private $description;
    private $link;
    private $imageFilename;


    /**
     * Erstellt ein neues Genre-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->genreId = $data['GenreID'];
        $this->genreName = $data['GenreName'] ?? '';
        $this->era = $data['Era'] ?? '';
        $this->description = $data['Description'] ?? '';
        $this->link = $data['Link'] ?? '';

        $this->imageFilename =
            $data['ImageFileName']
            ?? $data['ImageFilename']
            ?? $data['imageFilename']
            ?? $data['imagefilename']
            ?? $data['GenreID']
            ?? null;
    }

    // Getter

    /**
     * Gibt die ID des Genres zurück
     *
     * @return int Die Genre-ID
     */
    public function getGenreID()
    {
        return $this->genreId;
    }

    /**
     * Gibt den Namen des Genres zurück
     *
     * @return string Der Genre-Name
     */
    public function getGenreName()
    {
        return $this->genreName;
    }

    /**
     * Gibt die Epoche des Genres zurück
     *
     * @return string Die Epoche
     */
    public function getEra()
    {
        return $this->era;
    }

    /**
     * Gibt die Beschreibung des Genres zurück
     *
     * @return string Die Beschreibung
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Gibt den Link zu weiteren Informationen über das Genre zurück
     *
     * @return string Der Link
     */
    public function getLink()
    {
        return $this->link;
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
