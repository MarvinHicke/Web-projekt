<?php

/**
 * Repräsentiert einen Kunden (Customer) mit seinen Kontaktdaten aus der Datenbank
 */
class customer
{
    private $customerID;
    private $firstName;
    private $lastName;
    private $address;
    private $city;
    private $region;
    private $country;
    private $postal;
    private $phone;
    private $email;

    /**
     * Erstellt ein neues Customer-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $row Ein Array mit den Datenbankwerten
     */
    public function __construct($row = [])
    {
        $this->customerID = $row['CustomerID'] ?? null;
        $this->firstName  = $row['FirstName'] ?? null;
        $this->lastName   = $row['LastName'] ?? null;
        $this->address    = $row['Address'] ?? null;
        $this->city       = $row['City'] ?? null;
        $this->region     = $row['Region'] ?? null;
        $this->country    = $row['Country'] ?? null;
        $this->postal     = $row['Postal'] ?? null;
        $this->phone      = $row['Phone'] ?? null;
        $this->email      = $row['Email'] ?? null;
    }

    // Getter

    /**
     * Gibt die Kunden-ID zurück
     *
     * @return int Die Kunden-ID
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * Gibt den Vornamen zurück
     *
     * @return string Der Vorname
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Gibt den Nachnamen zurück
     *
     * @return string Der Nachname
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Gibt die Adresse zurück
     *
     * @return string Die Adresse
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Gibt die Stadt zurück
     *
     * @return string Die Stadt
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Gibt die Region oder das Bundesland zurück
     *
     * @return string Die Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Gibt das Land zurück
     *
     * @return string Das Land
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Gibt die Postleitzahl zurück
     *
     * @return string Die Postleitzahl
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Gibt die Telefonnummer zurück
     *
     * @return string Die Telefonnummer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Gibt die E-Mail-Adresse zurück
     *
     * @return string Die E-Mail-Adresse
     */
    public function getEmail()
    {
        return $this->email;
    }
}