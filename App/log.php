<?php

use App\Mysql;
use App\Session;
use App\Token;

function exist($email, $pass, $hash=1): int{
    $db = Mysql::getInstance();
    if($hash){
        $pass = sha1($pass);
    }
    $req = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $req->execute([$email, $pass]);
    if($req->rowCount() == 1){
        $req = $req->fetch();
        if($req['isActive'] == 1){
            return $req['id'];
        }else{
            if($req['isActive'] == 3){
                $_SESSION['errors'][] = "Votre compte est entrain de subir un changement de mot de passe. Contactez un administrateur si vous n'etes pas à l'origine de cette requete.";
            }elseif($req['isActive'] == 0){
                $_SESSION['errors'][] =  "Votre compte a été bannis.";
            }else{
                $_SESSION['errors'][] =  "Votre compte n'est pas activé. Vérifiez vos emails.";
            }
        }
    }else{
        $_SESSION['errors'][] =  "Votre email ou votre mot de passe est incorrect.";

    }
    return 0;
}




function connect(array $data, int $id, $hash = 1): void{
    $session = new Session();
    if($session->isConnected()){
        $email = $data[0];
        $pass = $data[1];
        if($hash){
            $pass = sha1($data[1]);
        }
        if($data[2]){
            setcookie("email", $email, time() + 3600, '/');
            setcookie("password", $pass, time() + 3600, '/');
        }
        $_SESSION['state'] = "connected";
        $_SESSION['success'][] = "Vous etes bien connecté !";
        $_SESSION['id'] = $id;
        header("Location:/account");

    }else{
        $_SESSION['errors'][] = "Vous etes déjà connecté !";
    }
}

function logout(): void{
    $session = new Session();
    if($session->isConnected()){
        session_unset();
        session_destroy();
        setcookie('email', '', 0);
        setcookie('password', '', 0);
        header('Location: /');
    }else{
        $_SESSION['errors'][] = "Vous n'etes pas connecté !";
    }
}

function register($last, $first, $email, $pass, $repass): int{
    $db = Mysql::getInstance();
    if(!empty($_POST)){
        $nbr = 8;
        $lname = ucfirst(mb_strtolower($last, "UTF-8"));
        $fname = ucfirst(mb_strtolower($first, "UTF-8"));
        $email = mb_strtolower($email, "UTF-8");
        $pass = utf8_decode($pass);
        $repass = utf8_decode($repass);

        if(empty($lname)){$_SESSION['errors'][] = "Vous n'avez pas renseigné votre nom.";}
        if(empty($fname)){$_SESSION['errors'][] = "Vous n'avez pas renseigné votre prénom.";}
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){$_SESSION['errors'][] =  "Votre email est incorrect.";}
        if(empty($pass)){$_SESSION['errors'][] =  "Vous n'avez pas noté votre mot de passe.";return 0;}
        if (!preg_match('#^(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#', $pass)) {$_SESSION['errors'][] =  "Votre mot de passe n'est pas conforme. Il doit comporter $nbr caractères dont une majuscule, un chiffre et un caractère spécial.";}
        if($pass != $repass){$_SESSION['errors'][] =  "Vos mot de passe ne correspondent pas.";}
        if(empty($_SESSION['errors'])){
            $reqmail = $db->prepare("SELECT * FROM users WHERE email = ?");
            $reqmail->execute([$email]);
            if($reqmail->rowCount() == 0){
                $fullname = $lname . " " . $fname;
                $pass = sha1($pass);
                $token = new Token(30);
                $req = $db->prepare("INSERT INTO users SET fullname = ?, email = ?, password = ?, token_activation = ?");
                $req->execute([$fullname, $email, $pass, $token]);

                // Préparation du mail contenant le lien d'activation
                $destinataire = $email;
                $sujet = "Activation de votre compte Carry'Air" ;
                //$entete = "From: verification@m2si-developpement.com" ;

                // Le lien d'activation est composé de la clé(cle)
                $message = 'Bienvenue sur Carry\'Air,
          Pour activer votre compte, veuillez cliquer sur le lien ci dessous
          ou copier/coller dans votre navigateur internet.

          http://'.$_SERVER['SERVER_NAME'].'/activation/'.urlencode($token).'


          ---------------
          Ceci est un mail automatique, merci de ne pas y répondre.';


                mail($destinataire, $sujet, utf8_decode($message)) ; // Envoi du mail
                return 1;

                //header('Location: /?view=forum');
            }else{$_SESSION['errors'][] = "Cet email est déjà utilisé.";}
        }


    }
    return 0;
}

