<?php

class artist {
    private $id;
    private $firstName;
    private $lastName;
    private $nationality;
    private $gender;
    private $birthYear;

    public function __construct($data)
    {
        $this->id = $data['ArtistID'];
        $this->firstName = $data['FirstName'];
        $this->lastName = $data['LastName'];
        $this->nationality = $data['Nationality'];
        $this->gender      = isset($data['Gender']) ? $data['Gender'] : (isset($data['gender']) ? $data['gender'] : 'unbekannt');
        $this->birthYear   = isset($data['BirthYear']) ? $data['BirthYear'] : (isset($data['birthyear']) ? $data['birthyear'] : null);
    }

    // Getter

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getNationality()
    {
        return $this->nationality;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getBirthYear()
    {
        return $this->birthYear;
    }

    public function getId()
    {
        return $this->id;
    }
}

?>