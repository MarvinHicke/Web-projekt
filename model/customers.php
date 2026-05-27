<?php

class customer
{
    public $customerID;
    public $firstName;
    public $lastName;
    public $address;
    public $city;
    public $region;
    public $country;
    public $postal;
    public $phone;
    public $email;

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

    public function getCustomerID()
    {
        return $this->customerID;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPostal()
    {
        return $this->postal;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getEmail()
    {
        return $this->email;
    }
}