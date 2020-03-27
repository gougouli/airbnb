<?php

use App\Mysql;
use App\Session;
use App\Token;






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



