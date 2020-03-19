<?php

namespace App;


use PDO;

class Mysql{

    private $host = "localhost";
    private $dbname = "webproject";
    private $user = "root";
    private $pass = "";

    public function dbConnect(): PDO
    {
        try {
            $db = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->user, "$this->pass");
            return $db;
        } catch (\PDOException $e) {
            exit("Une erreur est survenue. Erreur #1");
        }
    }


}
