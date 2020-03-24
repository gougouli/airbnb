<?php


namespace App;

use App\Mysql;
use PDO;

class AccomodationList{

    public function getAll(): array{
        $db = Mysql::getInstance();
        $req = $db->query("SELECT * FROM accomodation");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRandom(int $length): array{
        $db = Mysql::getInstance();
        $req = $db->query("SELECT * FROM accomodation ORDER BY rand() LIMIT $length");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById(int $id): array{
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTop(int $length): array{
        $db = Mysql::getInstance();
        $req = $db->query("SELECT * FROM accomodation ORDER BY rating DESC LIMIT $length");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBySeller(int $id): array{
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id_seller = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByPlace($place){
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id_place IN (SELECT id FROM place WHERE city = ?)");
        $req->execute([$place]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    function getByPeople($people){
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM accomodation WHERE size >= ?");
        $req->execute([$people]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    function getByPlacePeople($where, $people){
        $mysql = Mysql::getInstance();
        $req = $db->prepare("SELECT id FROM accomodation WHERE size >= ? INTERSECT SELECT id FROM accomodation WHERE id_place IN (SELECT id FROM place WHERE city = ?)");
        $req->execute([$people, $where]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
