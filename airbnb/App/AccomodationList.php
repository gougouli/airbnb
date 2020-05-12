<?php


namespace App;

use App\Mysql;
use PDO;

class AccomodationList{

    public function getAll(): array{
        $utils = new Utils();
        $db = Mysql::getInstance();
        $req = $db->query("SELECT * FROM accomodation");
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $newList = [];
        foreach ($results as $result){
                $place = new Place();
                $result['place'] = $place->getPlace($result['id']);
                $result['img'] = $utils->getImage($result['id'], "acco", 1);
                $newList[] = $result;
        }
        return $newList;
    }

    public function getRandom(int $length): array{
        $db = Mysql::getInstance();
        $req = $db->query("SELECT * FROM accomodation ORDER BY rand() LIMIT $length");
        $results = $req->fetchAll();
        $newList = [];
        foreach ($results as $result){
            $place = new Place();
            $utils = new Utils();
            $result['place'] = $place->getPlace($result['id_place']);
            $result['img'] = $utils->getImage($result['id'], "acco");
            $newList[] = $result;
        }
        return $newList;
    }
    public function getById(int $id): array{
        $utils = new Utils();
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM accomodation WHERE id = ?");
        $req->execute([$id]);
        $home = $req->fetch(PDO::FETCH_ASSOC);
        $home['img'] = $utils->getImage($home['id'], "acco", 3);
        return $home;
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
    public function getByterms($min, $max, $place, $start, $end, $people){
        $db = Mysql::getInstance();
        $req = "SELECT * FROM accomodation WHERE price BETWEEN :price_min AND :price_max";
        $params = ["price_min" =>$min, "price_max" => $max];
        if($place != NULL){
            $req .= " AND id_place IN (SELECT id FROM place WHERE city LIKE :where OR country LIKE :where)";
            $params["where"] = "%$place%";
        }
        if($people != NULL){
                $req .= " AND size >= :people";
                $params["people"] = $people;
        }
        if($start != NULL && $end != NULL){
                $req .= " AND id IN (SELECT id_accomodation FROM booking WHERE 
                (:startdate < start_date AND :enddate < start_date) 
                OR (:startdate > end_date AND :enddate > end_date)) 
                OR id NOT IN (SELECT id_accomodation FROM booking)";
                $params["startdate"] = $start;
                $params["enddate"] = $end;
        }
        $stmt = $db->prepare($req);
        $stmt->execute($params);
        $listHouse = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newList= [];
        $utils = new Utils();
        foreach ($listHouse as $house){
                $place = new Place();
                $info = $place->getPlace($house['id_place']);
                $house['place'] = $info;
                $house['img'] = $utils->getImage($house['id'], "acco", 1);
                $newList[] = $house;
        }
        return $newList;

    }
}
