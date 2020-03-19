<?php

use App\Mysql;

function getErrors(){
    if(isset($_SESSION['errors'])){
        $errors =$_SESSION['errors'];
        unset($_SESSION['errors']);
        return $errors;
    }
    return FALSE;
}

function getFieldsValue(){
    if(isset($_POST)){
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
