<?php


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
