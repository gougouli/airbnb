<?php
session_start();
require_once "../vendor/autoload.php";
require_once "../App/functions.php";
require_once "../App/log.php";

use App\AccomodationList;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$url = $_GET['url'];
$url = explode("/",trim($url, "/"));
$page = isset($url['0']) ? $url['0'] : "/";
$parameter = isset($url['1']) ? $url['1'] : FALSE;

$loader = new FilesystemLoader('../views/page/');
$twig = new Environment($loader, [
    'cache' => false //'../tmp',
]);

$twig->addGlobal('session', $_SESSION);
//$twig->addGlobal('server', $_SERVER);

$accomodationList = new AccomodationList();

if(!isConnected()){
    if(isset($_COOKIE['email']) && isset($_COOKIE['password'])){
        if ($id = exist($_COOKIE['email'], $_COOKIE['password'], 0)) {
            $data=[$_COOKIE['email'], $_COOKIE['password'], 1];
            connect($data, $id, 0);
        }
    }
}

//====================== DEBUT Partie Connexion / Incription / Deconnexion ======================
if($page == "login"){
    if(!isConnected()){
        if(!empty($_POST)) {
            if (isset($_POST['email']) && isset($_POST['pass'])) {
                if ($id = exist($_POST['email'], $_POST['pass'])) {
                    if(empty($_POST['keep_pass'])){
                        $keep = FALSE;
                        echo "test";
                    }else{
                        $keep = true;
                    }
                    $data = [$_POST['email'], $_POST['pass'], $keep];
                    connect($data, $id);
                }
            }
        }
        echo $twig->render("login.twig",[
            "errors" => getMessage("errors"),
            "values" => getFieldsValue()
        ]);
    }

}
elseif($page == "register") {
    if(!isConnected()){
        if (!empty($_POST)) {
            if (isset($_POST['lname']) && isset($_POST['fname']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['repass'])) {
                if (register($_POST['lname'], $_POST['fname'], $_POST['email'], $_POST['pass'], $_POST['repass'])) {
                    header("Location: /");
                }
            }
            $_SESSION['errors'][] = "Vous n'avez pas renseigné tous les champs.";
        }
        echo $twig->render("register.twig", [
            "errors" => getMessage("errors"),
            "values" => getFieldsValue()
        ]);
    }

}
elseif($page == "logout"){
    if(isConnected()) {
        disconnect();
    }else{
        header('Location: /');
    }
}
//====================== FIN Partie Connexion / Incription / Deconnexion ======================

//====================== DEBUT Partie ACCOUNT ======================
elseif($page == "account"){
    if(isConnected()) {
        echo $twig->render("account.twig",[
            "errors" => getMessage("errors"),
            "success" => getMessage("success"),
            "info" => getInfoUser($_SESSION['id'])
        ]);
    }else{
        header('Location: /');
    }
}
//====================== FIN Partie ACCOUNT ======================

//====================== DEBUT Partie FORGOT PASS ======================

elseif($page == "forgot-pass"){
    if(!isConnected()) {
        if(!empty($_POST['email'])){
            $email = $_POST['email'];
            forgotpass($email);
        }
        echo $twig->render("forgot-pass.twig",[
            "errors" => getMessage("errors")
        ]);

    }else{
        header('Location: /');
    }
}

//====================== fin Partie FORGOT PASS ======================

//====================== DEBUT Partie NEW PASS ======================
//http://localhost/new-pass/'.urlencode($id).'

elseif($page == "new-pass") {
    if (!isConnected() && $parameter) {
        if (!empty($_POST['pass']) && !empty($_POST['repass'])) {
            $pass = $_POST['pass'];
            $repass = $_POST['repass'];
            $id = $_POST['idtoken'];
            newpass($id, $pass, $repass);
        }
        echo $twig->render("new-pass.twig", [
            "errors" => getMessage("errors"),
            "id" => $parameter
        ]);
    }
}
//====================== FIN Partie NEW PASS ======================

//====================== DEBUT Partie detail ======================

elseif($page == "detail") {
    if ($parameter) {
        echo $twig->render("detail.twig", [
            "errors" => getMessage("errors"),
            "id" => $parameter
        ]);
    }
    else {
        header("location:/");
    }
}
//====================== FIN Partie detail ======================


//====================== DEBUT Partie HOST ======================
elseif($page == "host"){
    require_once "../App/create_acco.php";
    if(isConnected()) {
        echo $twig->render("host.twig",[
            "errors" => getMessage("errors"),
            "success" => getMessage("success"),
        ]);
    }else{
        header('Location: /');
    }
}

//====================== fin Partie HOST ======================



//====================== DEBUT Partie NEW PASS ======================
//http://localhost/new-pass/'.urlencode($id).'

elseif($page == "list-detail") {
    if(isset($_POST)){
        if(isset($_POST['pl'])){$where = $_POST['pl'];}else{$where=0;}
        if(isset($_POST['pe'])){$people = $_POST['pe'];}else{$people=0;}
    }
    echo $twig->render("list-detail.twig", [
        "errors" => getMessage("errors"),
        "id" => $parameter,
        "accolist" => getList($where, $people)
    ]);
}
//====================== FIN Partie NEW PASS ======================



//====================== DEBUT Partie ACCUEIL ======================

else {
    echo $twig->render("home.twig", [
        "accomodations_random" => $accomodationList->getRandom(10),
        "accomodations_top" => $accomodationList->getTop(6),
        "errors" => getMessage("errors"),
        "success" => getMessage("success"),
    ]);
}
//====================== FIN Partie ACCUEIL ======================
