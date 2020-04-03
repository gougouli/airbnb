<?php

namespace App;


use PDO;

class Mysql extends PDO{

    private $host = "localhost";
    private $dbname = "webproject";
    private $user = "root";
    private $pass = "";

    private static $instance = null;
    private $PDOInstance = null;

    public function __construct()
    {
        try {
                $this->PDOInstance = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->user, $this->pass);
        } catch (\PDOException $e) {
            exit("Une erreur est survenue. Erreur #1");
        }
    }

    public static function getInstance() {

        if(is_null(self::$instance)) {
            self::$instance = new Mysql();
        }

        return self::$instance;
    }

    public function query($query, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        return $this->PDOInstance->query($query);
    }

    public function prepare($query, $options = NULL): \PDOStatement
    {
        return $this->PDOInstance->prepare($query);
    }

    public function execute($query): \PDOStatement
    {
        return $this->PDOInstance->execute($query);
    }

//    public function destroy()
//    {
//        self::$instance = Null;
//        return self::$instance;
//    }
//
//    public function renew()
//    {
//        $this->destroy();
//        return Mysql::getInstance();
//    }



}
