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
    private $gender;
    private $birthYear;
    private $imageFilename;

    /**
     * Erstellt ein Artist-Objekt anhand eines Datensatzes aus der Datenbank
     *
     * @param array $data Array mit Datenbankwerten
     */
    public function __construct($data)
    {
        $this->id = $data['ArtistID'];
        $this->firstName = $data['FirstName'];
        $this->lastName = $data['LastName'];
        $this->nationality = $data['Nationality'];
        $this->gender      = isset($data['Gender']) ? $data['Gender'] : (isset($data['gender']) ? $data['gender'] : 'unbekannt');
        $this->birthYear   = isset($data['BirthYear']) ? $data['BirthYear'] : (isset($data['birthyear']) ? $data['birthyear'] : null);

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
     * Gibt das Geschlecht des Künstlers zurück
     *
     * @return string Das Geschlecht
     */
    public function getGender()
    {
        return $this->gender;
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
     * Gibt die eindeutige ID des Künstlers zurück
     *
     * @return int Die ID
     */
    public function getId()
    {
        return $this->id;
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
