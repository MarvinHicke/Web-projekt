<?php

class CustomerLogon
{
    public $customerID;
    public $userName;
    public $pass;
    public $salt;
    public $state;
    public $type;
    public $dateJoined;
    public $dateLastModified;

    // Der Konstruktor befüllt das Objekt direkt aus dem Datenbank-Array
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

    public function getCustomerID()
    {
        return $this->customerID;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDateJoined()
    {
        return $this->dateJoined;
    }

    public function getDateLastModified()
    {
        return $this->dateLastModified;
    }
}