<?php
use App\Mysql;


function token($length) {
    $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}
function getMessage($type){
    if(isset($_SESSION[$type])){
        $errors = $_SESSION[$type];
        $_SESSION[$type] = [];
        return $errors;
    }
    return FALSE;
}

function getFieldsValue(){
    if(!empty($_POST)){
        $values =$_POST;
        //unset($_POST);
        return $values;
    }
    return FALSE;
}

function validate($id): void{
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("UPDATE user SET isActive = 1 WHERE id = ?");
    $stmt->execute([$id]);
}
function getAccomodationByUser($id){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("SELECT * FROM accomodation WHERE id_seller = ?");
    $stmt->execute([$id]);
    //var_dump($stmt->fetch(PDO::FETCH_ASSOC));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getInfoUser($id){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $info['accomodation'] = getAccomodationByUser($id);
    //var_dump($info);
    return $info;
}
