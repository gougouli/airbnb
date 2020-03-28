<?php


namespace App;


class Utils
{
    function getFieldsValue(){
        if(!empty($_POST)){
            $values =$_POST;
            return $values;
        }elseif(!empty($_GET)){
            $values = $_GET;
            return $values;
        }
        return FALSE;
    }
}
