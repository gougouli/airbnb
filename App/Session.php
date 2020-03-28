<?php


namespace App;


class Session
{
    private static $instance;

    public static function getInstance() {

        if(is_null(self::$instance)) {
            self::$instance = new Session();
        }
        return self::$instance;
    }
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

    public function disconnect(){
        if($this->isConnected()){
            setcookie("email","");
            setcookie("password","");
            $_SESSION['success'] = [];
            $_SESSION['success'][] = "Vous vous êtes bien déconnecté !";
            header('Location: /');
            exit("Redirection désactivé sur votre navigateur.");
        }else{
            $_SESSION['errors'][] = "Vous n'etes pas connecté !";
        }
    }
}
