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

function newAdress($country, $city, $address, $sub_address, $zip,$lat,$long){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    //$req = $db->prepare("INSERT INTO place SET country = ?, city = ?, address = ?, sub_address = ?, zip = ?,lat = ?, lon = ?");
    $req = $db->prepare("INSERT INTO place (country, city, address, sub_address, zip, lat, lon) VALUES (?,?,?,?,?,?,?)");
    $req->execute([$country, $city, $address, $sub_address, $zip, $lat, $long]);
    return $req;
}



function getPlaceId($lat, $lon){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("SELECT * FROM place WHERE lat = $lat");
    $stmt->execute([$lat]);

    $stmt = $stmt->fetch();
    return $stmt['id'];

}
function getCoords($address, $city, $zip){
    //https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q=7+place+de+la+resistance+vourles+69390&format=json
    $url = 'https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q='.$address .'+'.$city.'+'.$zip.'&format=json';
    //echo "$url";
    $page ='';
    $fh = fopen($url,'r') or die($php_errormsg);
    while (! feof($fh)) { $page .= fread($fh,1048576); }
    fclose($fh);
    $test1 = explode('"lat":"', $page);
    $coords = explode(",", $test1[1]);
    $lat = trim($coords[0],'"');

    $long = $coords[1];
    $long= trim(str_replace('"lon":"', "", $long),'"');
    $coords=[$lat,$long];
    //var_dump($coords);
    return $coords;
}


function newAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end){

    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $req = $db->prepare("INSERT INTO accomodation (title, content, size, id_seller, animal, handicap, breakfast, dinner, single_bed, double_bed, other, id_place, price, hour_start, hour_end) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $req->execute([$title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$id_place,$price,$hour_start,$hour_end]);
    return $req;
}
