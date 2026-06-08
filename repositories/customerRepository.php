<?php
//
// Test-Klassen müssen aktuallisiert werden c - s!
//

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
     * Holt alle Kunden inklusive ihrer zugehörigen Login-Daten aus der Datenbank
     *
     * @return array Ein Array aus Arrays mit den Kunden- und Logon-Daten
     */
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
        $sql = "SELECT c.*, cl.Pass, cl.Type, cl.State, cl.CustomerID 
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
                cl.UserName = :email,
                cl.DateLastModified = NOW()
            WHERE c.CustomerID = :id";

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
     * Ändert den Account-Status. Schützt den letzten Admin vor Sperrung
     *
     * @param int $id Die ID des Accounts
     * @param int $state Der neue Status (1 = Aktiv, 0 = Gesperrt)
     * @return bool True, wenn der Status geändert wurde, false bei Schutzverletzung des letzten Admins
     */
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

    /**
     * Erhebt einen normalen Kunden in den Administrator-Status (Type = 2)
     *
     * @param int $id Die ID des Kunden
     * @return bool True bei Erfolg, andernfalls false
     */
    public function elevateToAdmin($id)
    {
        $sql = "UPDATE customerlogon SET Type = 2 WHERE CustomerID = :id";
        $stmt = $this->db->preparedStatement($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Ermittelt die Anzahl aller aktiven Administratoren
     *
     * @return int Die Anzahl der aktiven Admins
     */
    public function getActiveAdminCount()
    {
        $sql = "SELECT COUNT(*) AS Count FROM customerlogon WHERE Type = 2 AND State = 1";
        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetch()['Count'];
    }

    /**
     * Stuft einen Administrator zurück zum normalen Kunden (Type = 1). Schützt den letzten Admin vor der Abstufung
     *
     * @param int $id Die ID des Administrators
     * @return bool True bei Erfolg, false wenn es der letzte aktive Admin ist
     */
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