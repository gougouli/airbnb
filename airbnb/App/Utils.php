<?php


namespace App;


use PDO;

class Utils
{
    public function getFieldsValue(){
        if(!empty($_POST)){
            $values =$_POST;
            if($_FILES){
                $values['file'] = $_FILES;
//                var_dump($values['file']);
            }
            return $values;
        }elseif(!empty($_GET)){
            $values = $_GET;

            return $values;
        }
        return FALSE;
    }

    public function lastInsertId($table){
        $db = Mysql::getInstance();
        $req = $db->query("SELECT id FROM $table ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        return $req['id'];
    }
    public function getImage($id, $type){
        $db = Mysql::getInstance();
        $type = "id_".$type;
        $img =  $db->query("SELECT * FROM img WHERE $type = $id");
        return $img->fetchAll(PDO::FETCH_ASSOC);
    }

}
