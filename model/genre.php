<?php

/**
 * Repräsentiert ein Kunst-Genre mit seinen Details aus der Datenbank
 */
class genre
{
    private $genreid;
    private $genrename;
    private $era;
    private $description;
    private $link;

    /**
     * Erstellt ein neues Genre-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->genreid = $data['GenreID'];
        $this->genrename = $data['GenreName'];
        $this->era = $data['Era'];
        $this->description = $data['Description'];
        $this->link = $data['Link'];
    }

    // Getter

    /**
     * Gibt die ID des Genres zurück
     *
     * @return int Die Genre-ID
     */
    public function getGenreID()
    {
        return $this->genreid;
    }

    /**
     * Gibt den Namen des Genres zurück
     *
     * @return string Der Genre-Name
     */
    public function getGenreName()
    {
        return $this->genrename;
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
}

