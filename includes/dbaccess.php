<?php

require_once "./../config/dbconfig.php";
class dbaccess
{
    private $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    private $user = DBUSER;
    private $password = DBPASS;

    public $pdo;

    public function connect()
    {
        if($this->isConnected())
        {
            throw new Exception("DB already connected");
        }
        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $ex)
        {
            exit('DB connection failed: ' . $ex->getMessage());
        }
    }

    public function close()
    {
        if(!$this->isConnected())
        {
           return;
        }

        $this->pdo = null;
    }

    public function preparedStatement($sql)
    {
        if(!$this->isConnected())
        {
            throw new Exception("DB not connected");
        }

        return $this->pdo->prepare($sql);
    }

    public function isConnected()
    {
        return $this->pdo != null;
    }
}

?>

