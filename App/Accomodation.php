<?php


namespace App;

use App\Mysql;

class Accomodation {


    function newAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end){
        $db = Mysql::getInstance();
        $req = $db->prepare("INSERT INTO accomodation (title, content, size, id_seller, animal, handicap, breakfast, dinner, single_bed, double_bed, other, id_place, price, hour_start, hour_end) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);
        return $req;
    }

//    public function addAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end): void{
//        $db = Mysql::getInstance();
//        $req = $db->prepare("INSERT INTO accomodation SET
//               title = ?, content = ?, size = ?,  id_seller = ?,
//               animal = ?, handicap = ?, breakfast = ?, dinner = ?,
//               single_bed = ?, double_bed = ?, other = ?, id_place = ?,price = ?,
//               hour_start = ?, hour_end = ?");
//        $req = $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);
//    }

    public function getById($id){
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM `accomodation` WHERE `id` = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ACCOC);
    }

    public function disable():void { //  FUNCTION BDD////
        $db = Mysql::getInstance();
        $req = $db->prepare("UPDATE `accomodation` SET `isActive` = 0 WHERE `id` = ?");
        $req->execute([$this->token]);
    }

    public function enable(): void{ //  FUNCTION BDD
        $db = Mysql::getInstance();
        $req = $db->prepare("UPDATE `accomodation` SET `isActive` = 1 WHERE `id` = ?");
        $req->execute([$this->token]);
    }

    public function getAccomodationByUser($id){
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT * FROM gaccomodation WHERE id_seller = ?");
        $stmt->execute([$id]);
        //var_dump($stmt->fetch(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccomodationById($id){
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT * FROM accomodation WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result['place'] = getPlaceInfoById($id);
        //var_dump($stmt->fetch(PDO::FETCH_ASSOC));
        return $result;
    }




}
