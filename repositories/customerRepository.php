<?php

/**
 * Repository für Datenbankabfragen rund um Kunden (Customers) und deren Login-Daten.
 */
class customerRepository
{
    private $db;

    /**
     * Erstellt eine neue Instanz des customerRepository.
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess).
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Gibt alle Benutzerkonten mit Login-Daten nach CustomerID sortiert zurück.
     *
     * @return array Die Benutzerkonten
     */
    public function findAll()
    {
        $sql = "SELECT c.*, cl.UserName, cl.Type, cl.State, cl.DateJoined, cl.DateLastModified
                FROM customers c
                JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
                ORDER BY c.CustomerID ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sucht einen Kunden und seine Login-Daten anhand der Kunden-ID
     *
     * @param int $id Die ID des Kunden
     * @return array Das Array des Kundendatensatzes
     */
    public function GetById($id)
    {
        $sql = "SELECT c.*, cl.UserName, cl.Type, cl.State, cl.DateJoined
                FROM customers c, customerlogon cl
                WHERE c.CustomerID = cl.CustomerID AND c.CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Sucht die Login- und Kundendaten anhand des Benutzernamens
     *
     * @param string $username Der gesuchte Benutzername
     * @return array Das Array mit Passwort-Hash, Typ, Status und ID
     */
    public function GetByUsername($username)
    {
        $sql = "SELECT c.*, cl.UserName, cl.Pass, cl.Type, cl.State, cl.CustomerID 
                FROM customers c, customerlogon cl
                WHERE cl.CustomerID = c.CustomerID AND cl.UserName = :username";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Erstellt einen neuen Kunden inklusive Login-Account in einer sicheren Transaktionsreihenfolge
     *
     * @param array $cData Die Profildaten des Kunden
     * @param array $lData Die Login-Daten
     * @return bool True, wenn die Registrierung erfolgreich war, andernfalls false
     */
    public function create($cData, $lData)
    {
        try
        {
            $sqlLogon = "INSERT INTO customerlogon (UserName, Pass, Salt, Type, State, DateJoined, DateLastModified)
                        VALUES (:userName, :pass, '', 1, 1, NOW(), NOW())";

            $stmtL = $this->db->preparedStatement($sqlLogon);
            $stmtL->execute
            ([
                'userName' => $lData['UserName'],
                'pass' => $lData['Pass']
            ]);

            $customerId = $this->db->getPdo()->lastInsertId();

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

    /**
     * Aktualisiert die Profildaten eines bestehenden Kunden
     *
     * @param int $id Die ID des zu aktualisierenden Kunden
     * @param array $data Die neuen Profildaten
     * @return bool True bei Erfolg, andernfalls false
     */
    public function update($id, $data)
    {
        $sql = "UPDATE customers c
            JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
            SET c.FirstName = :firstName,
                c.LastName = :lastName,
                c.Address = :address,
                c.City = :city,
                c.Region = :region,
                c.Country = :country,
                c.Postal = :postal,
                c.Phone = :phone,
                c.Email = :email,
                cl.UserName = :userName,
                cl.DateLastModified = NOW()
            WHERE c.CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);

        return $stmt->execute([
            'firstName' => $data['FirstName'],
            'lastName'  => $data['LastName'],
            'address'   => $data['Address'],
            'city'      => $data['City'],
            'region'    => $data['Region'] ?? null,
            'country'   => $data['Country'],
            'postal'    => $data['Postal'],
            'phone'     => $data['Phone'],
            'email'     => $data['Email'],
            'userName'  => $data['Email'],
            'id'        => $id
        ]);
    }

    /**
     * Ändert das Passwort eines Kunden in der customerlogon-Tabelle
     *
     * @param int $id Die ID des Kunden
     * @param string $hashedPassword Der neue, bereits gehashte Passwort-String
     * @return bool True bei Erfolg, andernfalls false
     */
    public function updatePassword($id, $hashedPassword)
    {
        $sql = "UPDATE customerlogon 
            SET Pass = :pass,
                DateLastModified = NOW()
            WHERE CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);

        return $stmt->execute([
            'pass' => $hashedPassword,
            'id' => $id
        ]);
    }

    /**
     * Ändert den Account-Status und aktualisiert das Änderungsdatum.
     * Schützt den letzten aktiven Admin vor Deaktivierung.
     *
     * @param int $id Die ID des Accounts
     * @param int $state Der neue Status (1 = Aktiv, 0 = Deaktiviert)
     * @return bool True bei Erfolg, false bei ungültigem Status oder Schutzverletzung
     */
    public function updateState($id, $state)
    {
        $id = (int) $id;
        $state = (int) $state;

        if ($state !== 0 && $state !== 1)
        {
            return false;
        }

        if ($state === 0)
        {
            $user = $this->GetById($id);

            if ($user && (int) $user['Type'] === 2 && (int) $user['State'] === 1)
            {
                if ((int) $this->getActiveAdminCount() <= 1)
                {
                    return false;
                }
            }
        }

        $sql = "UPDATE customerlogon
            SET State = :state,
                DateLastModified = NOW()
            WHERE CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);

        return $stmt->execute
        ([
            'state' => $state,
            'id' => $id
        ]);
    }

    /**
     * Erhebt ein Benutzerkonto in den Administrator-Status und aktualisiert das Änderungsdatum.
     *
     * @param int $id Die ID des Benutzerkontos
     * @return bool True bei Erfolg, andernfalls false
     */
    public function elevateToAdmin($id)
    {
        $sql = "UPDATE customerlogon
            SET Type = 2,
                DateLastModified = NOW()
            WHERE CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);

        return $stmt->execute
        ([
            'id' => (int) $id
        ]);
    }

    /**
     * Ermittelt die Anzahl aller aktiven Administratoren
     *
     * @return int Die Anzahl der aktiven Admins
     */
    public function getActiveAdminCount()
    {
        $sql = "SELECT COUNT(*) AS Count
            FROM customerlogon
            WHERE Type = 2 AND State = 1";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['Count'];
    }

    /**
     * Stuft einen Administrator zum normalen Benutzer zurück und aktualisiert das Änderungsdatum.
     * Schützt den letzten aktiven Admin vor Abstufung.
     *
     * @param int $id Die ID des Benutzerkontos
     * @return bool True bei Erfolg, false bei Schutzverletzung des letzten aktiven Admins
     */
    public function demoteAdmin($id)
    {
        $id = (int) $id;
        $user = $this->GetById($id);

        if ($user && (int) $user['Type'] === 2 && (int) $user['State'] === 1)
        {
            if ((int) $this->getActiveAdminCount() <= 1)
            {
                return false;
            }
        }

        $sql = "UPDATE customerlogon
            SET Type = 1,
                DateLastModified = NOW()
            WHERE CustomerID = :id";

        $stmt = $this->db->preparedStatement($sql);

        return $stmt->execute
        ([
            'id' => $id
        ]);
    }
}