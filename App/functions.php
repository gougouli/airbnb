<?php

use App\AccomodationList;
use App\Mysql;


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


function getList($where = 0, $people = 0){
    $list = new AccomodationList();
    if($people){
        $listHouse = $list->getByPeople($people);
    }elseif($where) {
        $listHouse = $list->getByPlace($where);
    }elseif ($where && $people){
        $listHouse = $list->getByPlacePeople($where, $people);
    }else{
        $listHouse = $list->getAll();
    }

    $newList= [];
    foreach ($listHouse as $house){
        $info = getPlaceInfoById($house['id_place']);
        $house['infoplace'] = $info;
        $newList[] = $house;

    }
    return $newList;
}


