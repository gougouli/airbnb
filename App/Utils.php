<?php


namespace App;


class Utils
{
    function getFieldsValue(){
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
}
