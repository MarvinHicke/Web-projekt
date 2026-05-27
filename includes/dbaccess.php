<?php

require_once __DIR__ ."/../config/dbconfig.php";
class dbaccess
{
    private $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    private $user = DBUSER;
    private $password = DBPASS;

    private $pdo = null;

    public function getPdo()
    {
        if ($this->pdo === null) {
            $this->connect();
        }

        return $this->pdo;
    }

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

        try {
            $this->pdo = new PDO($dsn, DBUSER, DBPASS, [
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

    public function close()
    {
        if (!$this->isConnected()) {
            return;
        }

        $this->pdo = null;
    }

    public function preparedStatement(string $sql)
    {
        if (!$this->isConnected()) {
            throw new Exception("DB not connected");
        }

        return $this->getPdo()->prepare($sql);
    }

    public function isConnected()
    {
        return $this->pdo != null;
    }

}