<?php


namespace App;

use App\Mysql;
use PDO;

class Accomodation {


//    function newAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end){
//        $db = Mysql::getInstance();
//        $req = $db->prepare("INSERT INTO accomodation (title, content, size, id_seller, animal, handicap, breakfast, dinner, single_bed, double_bed, other, id_place, price, hour_start, hour_end) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
//        $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);
//        return $req;
//    }

    public function addAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end): void{
        $db = Mysql::getInstance();
        $req = $db->prepare("INSERT INTO accomodation SET
               title = ?, content = ?, size = ?,  id_seller = ?,
               animal = ?, handicap = ?, breakfast = ?, dinner = ?,
               single_bed = ?, double_bed = ?, other = ?, id_place = ?,price = ?,
               hour_start = ?, hour_end = ?");
        $req= $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);

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




}
