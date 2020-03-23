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
        return $values;
    }
    return FALSE;
}

function validate($id): void{
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("UPDATE users SET isActive = 1 WHERE id = ?");
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

function getInfoUser($id, $acco = 1){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    if($acco){
        $info['accomodation'] = getAccomodationByUser($id);
    }
    //var_dump($info);
    return $info;
}

function newAdress($country, $city, $address, $sub_address, $zip,$lat,$long){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
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

function forgotpass($email){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){$_SESSION['errors'][] = "Votre email est incorrect.";return;}
    $req = $db->prepare("SELECT * FROM users WHERE email = ?");
    $req->execute([$email]);
    if($req->rowCount() != 1){$_SESSION['errors'][] = "Ce compte n'existe pas.";return;}
    $req = $req->fetch();
    $id = $req['id'];
    if($req['isActive'] == 3){
        $_SESSION['errors'][] =  "Votre compte est entrain de subir un changement de mot de passe. Contactez un administrateur si vous n'etes pas à l'origine de cette requete.";
    }elseif($req['isActive'] == 2){
        $_SESSION['errors'][] =  "Votre compte n'est pas activé, vérifiez d'abord vos emails.";
    }elseif($req['isActive'] == 0){
        $_SESSION['errors'][] =  "Votre compte a été bannis.";
    }else{

        $destinataire = $email;
        $sujet = utf8_decode("Mot de passe oublié de votre compte Carry'Air");
//$entete = "From: verification@m2si-developpement.com" ;

// Le lien d'activation est composé du login(log) et de la clé(cle)
        $message =
            'Bienvenue sur Carry\'Air,
Pour activer votre compte, veuillez cliquer sur le lien ci dessous
ou copier/coller dans votre navigateur internet. Si vous n\'etes pas à l\'origine de cette demande, contactez un administrateur.

http://localhost/new-pass/'.urlencode($id).'


---------------
Ceci est un mail automatique, merci de ne pas y répondre.';

        mail($destinataire, $sujet, $message) ; // Envoi du mail
        $req = $db->prepare("UPDATE users SET isActive = ? WHERE id = ?");
        $req->execute([3, $id]);
        $_SESSION['success'][] =  "La demande de changement de mot de passe a bien été effectuée.";
        header('Location: /');
    }
}

function newpass($id, $pass, $repass){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    if(!empty($pass) && $pass == $repass){
        $info = getInfoUser($id, 0);
        if($info['isActive'] == 3){
            $req = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $req->execute([sha1($pass), $id]);
            validate($id);
            $_SESSION['success'][] =  "Le mot de passe a bien été changé.";
            header('Location: /');
            return 1;
        }else{
            $_SESSION['errors'][] = "Votre compte n'a pas fait l'objet d'une demande de mot de passe.";
        }
    }else{
        $_SESSION['errors'][] = "Les deux mots de passe ne correspondent pas.";
    }
}

