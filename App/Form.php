<?php


namespace App;


class Form
{
    public function forgotpass($email){
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

    public function newpass($id, $pass, $repass){
        $db = Mysql::getInstance();
        if(!empty($pass) && $pass == $repass){
            $user = new User();
            $info = $user->getInfoUser($id, 0);
            if($info['isActive'] == 3){
                $req = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $req->execute([sha1($pass), $id]);
                $user->validate($id);
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

    public function sendMessageHelp($email, $object, $message, $captcha){
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

    public function register($last, $first, $email, $pass, $repass): int{
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

}
