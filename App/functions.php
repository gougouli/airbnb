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
    $db->query("UPDATE user SET `isActive` = 1 WHERE `id` = '$id'");
}
