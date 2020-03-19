<?php


namespace App;

use App\Mysql;

class Accomodation {

    public function addAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end): void{
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("INSERT INTO accomodation SET
               title = ?, content = ?, size = ?,  id_seller = ?,
               animal = ?, handicap = ?, breakfast = ?, dinner = ?,
               single_bed = ?, double_bed = ?, other = ?, id_place = ?,price = ?,
               hour_start = ?, hour_end = ?");
        $req = $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);
    }

    public function getById($id){
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("SELECT * FROM `accomodation` WHERE `id` = ?");
        $req->execute([$id]);
        return $req->fetchAll(PDO::FETCH_ACCOC);
    }

    public function disable():void { //  FUNCTION BDD////
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("UPDATE `accomodation` SET `isActive` = 0 WHERE `id` = ?");
        $req->execute([$this->token]);
    }

    public function enable(): void{ //  FUNCTION BDD
        $mysql = new Mysql();
        $db = $mysql->dbConnect();
        $req = $db->prepare("UPDATE `accomodation` SET `isActive` = 1 WHERE `id` = ?");
        $req->execute([$this->token]);
    }



}
