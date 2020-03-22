<?php

use App\Mysql;

var_dump($_POST);
if(!empty($_POST)){

    $id_seller = $_POST['id_seller'];
    $title = $_POST['title']; // main information
    $size = $_POST['size'];
    $single_bed = $_POST['single_bed'];
    $double_bed = $_POST['double_bed'];
    $hour_start = $_POST['time_arrive'];
    $hour_end = $_POST['time_go'];
    $content = $_POST['description'];
    $country = $_POST['country']; // Place
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $address = $_POST['address'];
    $sub_address = $_POST['sub_address'];
    $price = $_POST['price'];
    $other = $_POST['other'];

    //$picture = $_POST['picture'];
    $password = $_POST['password'];

    if(isset($_POST['animal'])){$animal = 1;}else{$animal = 0;}
    if(isset($_POST['handicap'])){$handicap = 1;}else{$handicap = 0;}
    if(isset($_POST['dinner'])){$dinner = 1;}else{$dinner = 0;}
    if(isset($_POST['breakfast'])){$breakfast = 1;}else{$breakfast = 0;}
    if(isset($_POST['okey'])){$okey = 1;}else{$okey = 0;}
    if($size != $double_bed * 2 + $single_bed * 1){$_SESSION['errors'][] = "vous avez dû faire une erreur, le nombre de personne ne correspond pas au nombre de lit demandé. Un lit double équivaut à deux personnes et un lit simple à une seule.";}
    if(empty($title)){$_SESSION['errors'][] = "vous n'avez pas définis l'intitulé de votre hébergement.";}
    if(strlen($title) > 50){$_SESSION['errors'][] = "votre intitulé ne doit pas dépasser 50 caractères.";}
    if(empty($size)){$_SESSION['errors'][] = "vous n'avez pas définit le nombre de personne maximum de votre hébergement.";}
    if($size > 100){$_SESSION['errors'][] = "vous avez fait une erreur. 100 personnes ne tiennent pas dans un hébergement";}
    if($double_bed > 100){$_SESSION['errors'][] = "vous avez fait une erreur, 100 lits double, ça parait un peu trop.";}
    if($single_bed > 100){$_SESSION['errors'][] = "vous avez fait une erreur, 100 lits simple, ça parait un peu trop.";}
    if(empty($hour_start)){$_SESSION['errors'][] = "vous n'avez pas préciser à quelle heure les clients peuvent arriver.";}
    if(empty($hour_end)){$_SESSION['errors'][] = "vous n'avez pas préciser à quelle heure les clients doivent partir.";}
    if(empty($content)){$_SESSION['errors'][] = "nous vous demandons de décrire votre annonces (100 caractères).";}
    if(empty($country) or empty($city) or empty($zip) or empty($address)){$_SESSION['errors'][] = "vous n'avez pas completé l'adresse de votre annonce.";}
    if(empty($price)){$_SESSION['errors'][] = "vous n'avez pas définit le montant de votre annonce.";}
    if($okey == "false"){$_SESSION['errors'][] = "vous n'avez pas valider les conditions de vente.";}
    if(empty($password)){$_SESSION['errors'][] = "vous n'avez pas écrit votre mot de passe.";}


    $pdo = new Mysql();
    $db = $pdo->dbConnect();
    $req = $db->prepare("SELECT * FROM user WHERE id = ? AND password = ?");
    $req->execute([$id_seller, sha1($password)]);
    if($req->rowCount() == 1){
        $coords = getCoords($address, $city, $zip);
        $step1 = newAdress($country, $city, $address, $sub_address, $zip,$coords[0],$coords[1]);
        $place_id = getPlaceId($coords[0],$coords[1]);
        $request = newAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$place_id,$price,$hour_start,$hour_end);
//        if($step1 && $place_id && $request){
//            echo "ok";
//
//        }else{
//            echo $id_seller;
//            echo "coords"; var_dump($coords); echo "<br>";
//            echo "step1: ";var_dump($step1); echo "<br>";
//            echo "place_id: ";var_dump($place_id) ; echo "<br>";
//            echo "request: ";var_dump($request);  echo "<br>";

//        }
    }else{$_SESSION['errors'][] = "votre mot de passe est incorrect !";return;}
}
