<?php

/**
 * Repräsentiert einen Künstler mit seinen Details aus der Datenbank
 */
class artist
{
    private $id;
    private $firstName;
    private $lastName;
    private $nationality;
    private $birthYear;
    private $yearOfDeath;
    private $details;
    private $artistLink;
    private $imageFilename;
    private $width;
    private $height;

    /**
     * Erstellt ein Artist-Objekt anhand eines Datensatzes aus der Datenbank
     *
     * @param array $data Array mit Datenbankwerten
     */
    public function __construct($data)
    {
        $this->id = isset($data['ArtistID']) ? $data['ArtistID'] : null;
        $this->firstName = isset($data['FirstName']) ? $data['FirstName'] : null;
        $this->lastName = isset($data['LastName']) ? $data['LastName'] : null;
        $this->nationality = isset($data['Nationality']) ? $data['Nationality'] : null;
        $this->birthYear = isset($data['YearOfBirth']) ? $data['YearOfBirth'] : null;
        $this->yearOfDeath = isset($data['YearOfDeath']) ? $data['YearOfDeath'] : null;
        $this->details = isset($data['Details']) ? $data['Details'] : null;
        $this->artistLink = isset($data['ArtistLink']) ? $data['ArtistLink'] : null;

        $this->imageFilename =
            $data['ImageFileName']
            ?? $data['ImageFilename']
            ?? $data['imageFilename']
            ?? $data['imagefilename']
            ?? $data['ArtistID']
            ?? null;
    }

    // Getter

    /**
     * Gibt die eindeutige ID des Künstlers zurück
     *
     * @return int Die ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gibt den Vornamen des Künstlers zurück
     *
     * @return string Der Vorname
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Gibt den Nachname des Künstlers zurück
     *
     * @return string Der Nachname
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Gibt die Nationalität des Künstlers zurück
     *
     * @return string Die Nationalität
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Gibt das Geburtsjahr des Künstlers zurück
     *
     * @return int Das Geburtsjahr
     */
    public function getBirthYear()
    {
        return $this->birthYear;
    }

    /**
     * Gibt das Todesjahr des Künstlers zurück
     *
     * @return int Das Todesjahr
     */
    public function getYearOfDeath()
    {
        return $this->yearOfDeath;
    }

    /**
     * Gibt die Biografie/Details des Künstlers zurück
     *
     * @return string Die Details
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Gibt den externen Link zum Künstler zurück
     *
     * @return string Der Link
     */
    public function getArtistLink()
    {
        return $this->artistLink;
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

    /**
     * Gibt die Breite des Kunstwerks zurück
     *
     * @return int Die Breite
     */
    function getWidth ()
    {
        return $this->width;
    }

    /**
     * Gibt die Höhe des Kunstwerks zurück
     *
     * @return int Die Höhe.
     */
    function getHeight ()
    {
        return $this->height;
    }
}
