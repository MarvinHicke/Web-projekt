<?php

class customerRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $sql = "SELECT c.*, cl.UserName, cl.Type, cl.State, cl.DateJoined
                FROM customers c, customerlogon cl
                WHERE c.CustomerID = cl.CustomerID
                ORDER BY c.LastName ASC, c.FirstName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetById($id)
    {
        $sql = "SELECT c.*, cl.UserName, cl.Type, cl.State, cl.DateJoined
                FROM customers c, customerlogon cl
                WHERE c.CustomerID = cl.CustomerID AND c.CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetByUsername($username)
    {
        $sql = "SELECT c.*, cl.Pass, cl.Type, cl.State, cl.CustomerID 
                FROM customers c, customerlogon cl
                WHERE cl.CustomerID = c.CustomerID AND cl.UserName = :username";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($cData, $lData)
    {
        try
        {
            $sqlLogon = "INSERT INTO customerlogon (UserName, Pass, Type, State)
                     VALUES (:userName, :pass, 1, 1)";

            $stmtL = $this->db->preparedStatement($sqlLogon);
            $stmtL->execute
            ([
                'userName' => $lData['UserName'],
                'pass' => $lData['Pass']
            ]);

            $sqlId = "SELECT MAX(CustomerID) AS MaxID FROM customerlogon";
            $stmtId = $this->db->preparedStatement($sqlId);
            $stmtId->execute();
            $idRow = $stmtId->fetch(PDO::FETCH_ASSOC);
            $customerId = $idRow['MaxID'];

            $sqlC = "INSERT INTO customers (CustomerID, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
                     VALUES (:customerId, :firstName, :lastName, :address, :city, :region, :country, :postal, :phone, :email)";

            $stmtC = $this->db->preparedStatement($sqlC);
            $stmtC->execute
            ([
                'customerId' => $customerId,
                'firstName' => $cData['FirstName'] ?? '',
                'lastName' => $cData['LastName'] ?? '',
                'address' => $cData['Address'] ?? '',
                'city' => $cData['City'] ?? '',
                'region' => $cData['Region'] ?? null,
                'country' => $cData['Country'] ?? '',
                'postal' => $cData['Postal'] ?? '',
                'phone' => $cData['Phone'] ?? '',
                'email' => $cData['Email'] ?? ''
            ]);
            return true;
        } catch (Exception $ex)
        {
            echo "Datenbank-Fehler: " . $ex->getMessage() . "<br>";
            return false;
        }
    }

    public function update($id, $data)
    {
        $sql = "UPDATE customers 
                SET FirstName = :firstName, LastName = :lastName, Address = :address, 
                    City = :city, Region = :region, Country = :country, 
                    Postal = :postal, Phone = :phone, Email = :email 
                WHERE CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute
        ([
            'firstName' => $data['FirstName'],
            'lastName'  => $data['LastName'],
            'address'   => $data['Address'],
            'city'      => $data['City'],
            'region'    => $data['Region'] ?? null,
            'country'   => $data['Country'],
            'postal'    => $data['Postal'],
            'phone'     => $data['Phone'],
            'email'     => $data['Email'],
            'id'        => $id
        ]);
    }

    public function updatePassword($id, $hashedPassword)
    {
        $sql = "UPDATE customerlogon SET Pass = :pass WHERE CustomerID = :id";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute
        ([
            'pass' => $hashedPassword,
            'id' => $id
        ]);
    }

    public function updateState($id, $state)
    {
        if ($state == 0)
        {
            $user = $this->GetById($id);
            if ($user && $user['Type'] == 2)
            {
                if ($this->getActiveAdminCount() <= 1)
                {
                    return false;
                }
            }
        }

        $sql = "UPDATE customerlogon SET State = :state WHERE CustomerID = :id";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute
        ([
            'state' => $state,
            'id' => $id
        ]);
    }

    public function elevateToAdmin($id)
    {
        $sql = "UPDATE customerlogon SET Type = 2 WHERE CustomerID = :id";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getActiveAdminCount()
    {
        $sql = "SELECT COUNT(*) AS Count FROM customerlogon WHERE Type = 2 AND State = 1";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetch()['Count'];
    }

    public function demoteAdmin($id)
    {
        if($this->getActiveAdminCount() <= 1)
        {
            $user = $this->GetById($id);
            if($user && $user['Type'] == 2)
            {
                return false;
            }
        }


        $sql = "UPDATE customerlogon SET Type = 1 WHERE CustomerID = :id";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute(['id' => $id]);
    }
}