<?php


namespace App;

use App\Mysql;
use PDO;

class AccomodationList{

    public function getAll(): array{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->query("SELECT * FROM accomodation");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRandom(int $length): array{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->query("SELECT * FROM accomodation ORDER BY rand() LIMIT $length");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById(int $id): array{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTop(int $length): array{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->query("SELECT * FROM accomodation ORDER BY rating DESC LIMIT $length");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBySeller(int $id): array{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id_seller = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByPlace($place){
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("SELECT * FROM accomodation WHERE city = ?");
        $req->execute([$place]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    function getByPeople($people){
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id IN (SELECT id FROM accomodation WHERE size >= $people ) ");
        $req->execute([$people]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
