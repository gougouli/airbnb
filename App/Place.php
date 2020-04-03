<?php


namespace App;


use PDO;

class Place
{

//    public function getId($lat)
//    {
//        $db = Mysql::getInstance();
//        $req = $db->prepare("SELECT * FROM place WHERE lat = :lat");
//        $req->execute(["lat" => $lat]);
//        $req = $req->fetch();
//        return $req['id'];
//    }

    function addAdress($country, $city, $address, $sub_address, $zip, $lat, $long)
    {
        $db = Mysql::getInstance();
        $req = $db->prepare("INSERT INTO place (country, city, address, sub_address, zip, lat, lon) VALUES (?,?,?,?,?,?,?)");
        $req->execute([$country, $city, $address, $sub_address, $zip, $lat, $long]);
        if($req){
            return 1;
        }
        return 0;
    }

    function getCoords($address, $city, $zip)
    {
        //https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q=7+place+de+la+resistance+vourles+69390&format=json
        $url = 'https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q='.$address .'+'.$city.'+'.$zip.'&format=json';
        $page ='';
        $fh = fopen($url,'r') or die("Erreur avec API");
        while (! feof($fh)) {
            $page .= fread($fh,1048576);
        }
        fclose($fh);
        $test1 = explode('"lat":"', $page);
        $coords = explode(",", $test1[1]);
        $lat = trim($coords[0],'"');

        $long = $coords[1];
        $long= trim(str_replace('"lon":"', "", $long),'"');
        $coords=[$lat,$long];
        return $coords;
    }

    public function getPlace($id)
    {
        $db = Mysql::getInstance();
//        echo $id."-";
        $stmt = $db->prepare("SELECT * FROM place WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}
