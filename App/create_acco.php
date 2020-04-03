<?php

use App\Accomodation;
use App\Mysql;
use App\Place;

if(!empty($_POST)){
    $type_accepted = ["image/jpeg","image/jpg","image/png"];
    $pictures = $_FILES['picture'];
    $error = false;
    if (isset($_FILES['pictures'])) {
        foreach ($_FILES['pictures']['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                if(!in_array($_FILES["pictures"]["type"][$key], $type_accepted) && !$error){
                    $error = true;
                }
            }
        }
    }


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
    $password = $_POST['password'];
    if(isset($_POST['animal'])){$animal = 1;}else{$animal = 0;}
    if(isset($_POST['handicap'])){$handicap = 1;}else{$handicap = 0;}
    if(isset($_POST['dinner'])){$dinner = 1;}else{$dinner = 0;}
    if(isset($_POST['breakfast'])){$breakfast = 1;}else{$breakfast = 0;}
    if(isset($_POST['okey'])){$okey = 1;}else{$okey = 0;}
    if($size != $double_bed * 2 + $single_bed * 1){$_SESSION['errors'][] = "Vous avez dû faire une erreur, le nombre de personne ne correspond pas au nombre de lit inscris. Un lit double équivaut à deux personnes et un lit simple à une seule.";}
    if(empty($title)){$_SESSION['errors'][] = "Vous n'avez pas définis l'intitulé de votre hébergement.";}
    if(strlen($title) > 50){$_SESSION['errors'][] = "Votre intitulé ne doit pas dépasser 50 caractères.";}
    if(empty($size)){$_SESSION['errors'][] = "Vous n'avez pas définit le nombre de personne maximum de votre hébergement.";}
    if($size > 100){$_SESSION['errors'][] = "Vous avez fait une erreur. 100 personnes ne tiennent pas dans un hébergement";}
    if($double_bed > 100){$_SESSION['errors'][] = "Vous avez fait une erreur, 100 lits double, ça parait un peu trop.";}
    if($single_bed > 100){$_SESSION['errors'][] = "Vous avez fait une erreur, 100 lits simple, ça parait un peu trop.";}
    if(empty($hour_start)){$_SESSION['errors'][] = "Vous n'avez pas préciser à quelle heure les clients peuvent arriver.";}
    if(empty($hour_end)){$_SESSION['errors'][] = "Vous n'avez pas préciser à quelle heure les clients doivent partir.";}
    if(empty($content)){$_SESSION['errors'][] = "Nous vous demandons de décrire votre annonces (100 caractères).";}
    if(empty($country) or empty($city) or empty($zip) or empty($address)){$_SESSION['errors'][] = "Vous n'avez pas completé l'adresse de votre annonce.";}
    if(empty($price)){$_SESSION['errors'][] = "Vous n'avez pas définit le montant de votre annonce.";}
    if($okey == "false"){$_SESSION['errors'][] = "Vous n'avez pas valider les conditions de vente.";}
    if(empty($password)){$_SESSION['errors'][] = "Vous n'avez pas écrit votre mot de passe.";}
    if(sizeof($pictures['error']) < 3){$_SESSION['errors'][] = "Vous devez uploader au moins 3 photos.";}
    if($error){$_SESSION['errors'][] = "Une problème est survenu. Verifiez l'extension de vos fichiers puis ré-essayez.";}
    $db = Mysql::getInstance();
    $req = $db->prepare("SELECT * FROM users WHERE id = ? AND password = ?");
    $req->execute([$id_seller, sha1($password)]);
    if(!$_SESSION['errors']){
        if($req->rowCount() == 1){
            $erreur=0;

            for($i = 0; $i < sizeof($pictures['error']); $i++) {
                $filename = $_SESSION['id']."-".$pictures['name'][$i];
                $destination = __DIR__."/../public/img/upload/" . $filename;
                if (!move_uploaded_file($pictures['tmp_name'][$i], $destination)) {
                    $erreur++;
                }
            }
            if($erreur == 0){
                $util = new \App\Utils();
                $place = new Place();
                $acco = new Accomodation();

                $coords = $place->getCoords($address, $city, $zip);
                $place->addAdress($country, $city, $address, $sub_address, $zip,$coords[0],$coords[1]);
                $place_id = $util->lastInsertId('place');
//                var_dump($place_id);
                $acco->addAcco($title,$content,$size,$id_seller,$animal,$handicap,$breakfast,$dinner,$single_bed,$double_bed,$other,$place_id,$price,$hour_start,$hour_end);
                $id_acco = $util->lastInsertId('accomodation');
//                var_dump($id_acco);
                for($i = 0; $i < sizeof($pictures['error']); $i++) {
                    $filename = $pictures['name'][$i];
//                    $req = $db->prepare("INSERT INTO img SET id_acco = ?, SET name = ?");
                    $req = $db->prepare("INSERT INTO img (id_acco, name) VALUES (?,?)");
                    $req->execute([$id_acco, $filename]);
                }

                $_SESSION['success'][] = "Votre hebergement a bien été crée !";
                header('Location: /');
            }else{
                $_SESSION['errors'][] = "Il semblerait qu'il y ait eu des erreurs dans le transfert de votre fichier.";
            }
        }else{$_SESSION['errors'][] = "Votre mot de passe est incorrect !";}
    }

}
