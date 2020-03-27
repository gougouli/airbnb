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

function forgotpass($email){
    $db = Mysql::getInstance();
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
    $db = Mysql::getInstance();
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

function sendMessageHelp($email, $object, $message, $captcha){
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){$_SESSION['errors'][] = "Votre email est incorrect.";}
    if(empty($object)){$_SESSION['errors'][] = "Vous n'avez pas noté l'objet de votre message";}
    if(strlen($object) > 50){$_SESSION['errors'][] = "L'objet de votre message de doit pas contenir plus de 50 caractères";}
    if(empty($message)){$_SESSION['errors'][] = "Vous n'avez pas écrit de message.";}
    if(strlen($message) < 100){$_SESSION['errors'][] = "Votre message doit contenir au moins 100 caractères.";}
    if(empty($captcha)){$_SESSION['errors'][] = "Vous n'avez pas validé le recaptcha.";}
    if(empty($_SESSION['errors'])){
        $object_UTI = utf8_decode("Accusé d'envoi du mail au support du site.");
        $message_UTI = utf8_decode($message);

        $destinataireSUP = "gougouli69@outlook.fr";
        $enteteSUP = utf8_decode("Message Support: ($object)");

        $todaySUP = date('d/m/Y - H\hi\ms\s');
        //$entete = "From: verification@m2si-developpement.com" ;

        // Le lien d'activation est composé du login(log) et de la clé(cle)

        $messageSUP = utf8_decode("
     -> Date: $todaySUP;\n	
     -> Email de provenance: $email;
     -> Message reçu:
     $message_UTI\n
     -> Fin du message.
     -> Mail automatique du site Web. Ne pas répondre.");

        $message_UTI = utf8_decode("Votre message:\n $message_UTI");

        mail($destinataireSUP, $enteteSUP, $messageSUP) ; // Envoi du mail au support
        mail($email, $object_UTI, $message_UTI) ; // Envoi du mail a l'envoyer du mail  ++++++++++++ BUG +++++++++++++
        $_SESSION['success'][] = "Votre message a bien été envoyé.";
    }

}
