<?php

require_once __DIR__ ."/../config/dbconfig.php";

/**
 * Gibt das aktive PDO-Objekt zurück und stellt die Verbindung her
 */
class dbaccess
{
    private $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    private $user = DBUSER;
    private $password = DBPASS;

    private $pdo = null;

    /**
     * Gibt das aktive PDO-Objekt zurück und stellt die Verbindung her, falls noch nicht geschehen
     *
     * @return PDO Das aktive PDO-Objekt
     */
    public function getPdo()
    {
        if ($this->pdo === null)
        {
            $this->connect();
        }

        return $this->pdo;
    }

    /**
     * Baut die Verbindung zur Datenbank her
     */
    public function connect()
    {
        /*
        if($this->isConnected())
        {
            throw new Exception("DB already connected");
        }
        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        */
        $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8mb4';

        try
        {
            $this->pdo = new PDO($dsn, DBUSER, DBPASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch
            (PDOException $ex)
        {
            exit('DB connection failed: ' . $ex->getMessage());
        }
    }

    /**
     * Schließt die aktuelle Verbindung zur Datenbank
     */
    public function close()
    {
        if (!$this->isConnected())
        {
            return;
        }

        $this->pdo = null;
    }

    /**
     * Erstellt ein preparedStatement zur sicheren Ausführung von SQL-Befehlen
     *
     * @param string $sql Der auszuführende SQL-Befehl
     * @return PDOStatement Das vorbereitete Statement
     * @throws Exception Falls Datenbank nicht verbunden/gefunden ist
     */
    public function preparedStatement(string $sql)
    {
        if (!$this->isConnected())
        {
            throw new Exception("DB not connected");
        }

        return $this->getPdo()->prepare($sql);
    }

    /**
     * Prüft, ob eine Verbindung zur Datenbank besteht
     *
     * @return bool True, falls eine Verbindung besteht, sonst false
     */
    public function isConnected()
    {
        return $this->pdo != null;
    }
}