<?php

/**
 * Repräsentiert die Login-Daten eines Kunden (CustomerLogon) aus der Datenbank
 */
class CustomerLogon
{
    private $customerID;
    private $userName;
    private $pass;
    private $salt;
    private $state;
    private $type;
    private $dateJoined;
    private $dateLastModified;

    /**
     * Erstellt ein neues CustomerLogon-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $row Ein Array mit den Datenbankwerten
     */
    public function __construct($row = [])
    {
        $this->customerID       = $row['CustomerID'] ?? null;
        $this->userName         = $row['UserName'] ?? null;
        $this->pass             = $row['Pass'] ?? null;
        $this->salt             = $row['Salt'] ?? null;
        $this->state            = $row['State'] ?? null;
        $this->type             = $row['Type'] ?? 1;
        $this->dateJoined       = $row['DateJoined'] ?? null;
        $this->dateLastModified = $row['DateLastModified'] ?? null;
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
     * Gibt den Benutzernamen zurück
     *
     * @return string Der Benutzername
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Gibt das Passwort zurück
     *
     * @return string Das Passwort
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Gibt den Salt zurück
     *
     * @return string Der Salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Gibt den Account-Status zurück
     *
     * @return int Der Status
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Gibt den Account-Typ zurück
     *
     * @return int Der Typ
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gibt das Beitrittsdatum zurück
     *
     * @return string Das Datum
     */
    public function getDateJoined()
    {
        return $this->dateJoined;
    }

    /**
     * Gibt das Datum der letzten Änderung zurück
     *
     * @return string Das Datum
     */
    public function getDateLastModified()
    {
        return $this->dateLastModified;
    }
}