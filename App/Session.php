<?php


namespace App;


class Session
{
    public function isConnected(): int{
        if(isset($_SESSION['state']) && $_SESSION['state'] == "connected"){
            return TRUE;
        }
        return FALSE;
    }

    public function getMessage($type){
        if(isset($_SESSION[$type])){
            $errors = $_SESSION[$type];
            $_SESSION[$type] = [];
            return $errors;
        }
        return FALSE;
    }

    public function disconnect(){
        $_SESSION = [];
        setcookie("email","");
        setcookie("password","");
        $_SESSION['success'] = [];
        $_SESSION['success'][] = "Vous vous êtes bien déconnecté !";
        header('Location: /');
        exit("Redirection désactivé sur votre navigateur.");
    }
}
