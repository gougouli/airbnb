<?php


namespace App;


class Place
{
    private $id;

    public function __construct($country, $city, $address, $sub_address, $zip,$lat,$long)
    {
        $this->country = $country;
        $this->city = $city;
        $this->address = $address;
        $this->sub_address = $sub_address;
        $this->zip = $zip;
        $this->lat = $lat;
        $this->long = $long;
    }
    public function getId()
    {
        $db = Mysql::getInstance();
        $req = $db->prepare("SELECT * FROM place WHERE lat = :lat AND long = :long");
        $req->execute(["lat" => $this->lat,"long" => $this->long]);
        $this->id = $req;
        return $req;
    }

    function addAdress()
    {
        $db = Mysql::getInstance();
        $req = $db->prepare("INSERT INTO place (country, city, address, sub_address, zip, lat, lon) VALUES (?,?,?,?,?,?,?)");
        $req->execute([$this->country, $this->city, $this->address, $this->sub_address, $this->zip, $this->lat, $this->long]);
        if($req){
            return 1;
        }
        return 0;
    }

    function getCoords()
    {
        //https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q=7+place+de+la+resistance+vourles+69390&format=json
        $url = 'https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q='.$this->address .'+'.$this->city.'+'.$this->zip.'&format=json';
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

    public function getPlace()
    {
        if($this->id){
            $db = Mysql::getInstance();
            $stmt = $db->prepare("SELECT * FROM place WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            throw new \Exception("ID of place is not defined");
        }

    }

}
