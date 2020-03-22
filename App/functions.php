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

function forgotpass($email){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){$_SESSION['errors'][] = "Votre email est incorrect.";return;}
    $req = $db->prepare("SELECT * FROM user WHERE email = ?");
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
        $req = $db->prepare("UPDATE user SET isActive = ? WHERE id = ?");
        $req->execute([3, $id]);
        $_SESSION['success'][] =  "La demande de changement de mot de passe a bien été effectuée.";
    }

}
