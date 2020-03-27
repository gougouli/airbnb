<?php


namespace App;


class User
{
    public function getInfoUser($id, $acco = 1){
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($acco){
            $accolist = new AccomodationList();
            $info['accomodation'] = $accolist->getBySeller($id);
        }
        //var_dump($info);
        return $info;
    }

    public function validate($id): void{
        $db = Mysql::getInstance();
        $stmt = $db->prepare("UPDATE users SET isActive = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
}
