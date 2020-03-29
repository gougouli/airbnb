<?php
session_start();
require_once "../vendor/autoload.php";

use App\Accomodation;
use App\AccomodationList;
use App\Form;
use App\Session;
use App\User;
use App\Utils;
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
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');
$twig->addGlobal('session', $_SESSION);
//$twig->addGlobal('server', $_SERVER);

$accomodationList = new AccomodationList();
$session = Session::getInstance();
$user = new User();
$utils = new Utils();

if(!$session->isConnected()){
    if(isset($_COOKIE['email']) && isset($_COOKIE['password'])){
        if ($id = $user->exist($_COOKIE['email'], $_COOKIE['password'], 0)) {
            $data=[$_COOKIE['email'], $_COOKIE['password'], 1];
            $session->connect($data, $id, 0);
        }
    }
}

//====================== DEBUT Partie Connexion / Incription / Deconnexion ======================
if($page == "login"){
    if(!$session->isConnected()){
        if(!empty($_POST)) {
            if (isset($_POST['email']) && isset($_POST['pass'])) {
                if ($id = $user->exist($_POST['email'], $_POST['pass'])) {
                    if(empty($_POST['keep_pass'])){
                        $keep = FALSE;
                    }else{
                        $keep = true;
                    }
                    $data = [$_POST['email'], $_POST['pass'], $keep];
                    $session->connect($data, $id);
                }
            }
        }
        echo $twig->render("login.twig",[
            "errors" => $session->getMessage("errors"),
            "values" => $utils->getFieldsValue()
        ]);
    }else{
        $_SESSION['errors'][] = "Vous êtes déjà connecté.";
        header('Location: /');
    }

}
elseif($page == "register") {
    if(!$session->isConnected()){
        if (!empty($_POST)) {
            if (isset($_POST['lname']) && isset($_POST['fname']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['repass'])) {
                $form = new Form();
                if ($form->register($_POST['lname'], $_POST['fname'], $_POST['email'], $_POST['pass'], $_POST['repass'])) {
                    header("Location: /");
                }
            }
        }
        echo $twig->render("register.twig", [
            "errors" => $session->getMessage("errors"),
            "values" => $utils->getFieldsValue()
        ]);
    }else{
        $_SESSION['errors'][] = "Vous êtes déjà connecté.";
        header('Location: /');
    }

}
elseif($page == "logout"){
    if($session->isConnected()) {
        $session->disconnect();
    }else{
        $_SESSION['errors'][] = "Vous n'êtes pas connecté.";
        header('Location: /');
    }
}
//====================== FIN Partie Connexion / Incription / Deconnexion ======================

//====================== DEBUT Partie ACCOUNT ======================
elseif($page == "account"){
    if($session->isConnected()) {
        echo $twig->render("account.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
            "info" => $user->getInfoUser($_SESSION['id'])
        ]);
    }else{
        $_SESSION['errors'][] = "Vous n'êtes pas connecté.";
        header('Location: /');
    }
}
//====================== FIN Partie ACCOUNT ======================

//====================== DEBUT Partie FORGOT PASS ======================

elseif($page == "forgot-pass"){
    if(!$session->isConnected()) {
        if(!empty($_POST['email'])){
            $email = $_POST['email'];
            $form = new Form();
            $form->forgotpass($email);
        }
        echo $twig->render("forgot-pass.twig",[
            "errors" => $session->getMessage("errors")
        ]);

    }else{
        $_SESSION['errors'][] = "Vous devez vous déconnecter.";
        header('Location: /');
    }
}

//====================== fin Partie FORGOT PASS ======================

//====================== DEBUT Partie NEW PASS ======================
//http://localhost/new-pass/'.urlencode($id).'

elseif($page == "new-pass") {
    if (!$session->isConnected() && $parameter) {
        if (!empty($_POST['pass']) && !empty($_POST['repass'])) {
            $pass = $_POST['pass'];
            $repass = $_POST['repass'];
            $id = $_POST['idtoken'];
            $form = new Form();
            $form->newpass($id, $pass, $repass);
        }
        echo $twig->render("new-pass.twig", [
            "errors" => $session->getMessage("errors"),
            "id" => $parameter
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être déconnecté.";
        header('Location: /');
    }
}
//====================== FIN Partie NEW PASS ======================

//====================== DEBUT Partie detail ======================

elseif($page == "detail") {
    if ($parameter) {
        $acco = new Accomodation();
        $infoAcco = $acco->getAccomodationById($parameter);
        echo $twig->render("detail.twig", [
            "acco" => $infoAcco,
            "errors" => $session->getMessage("errors"),
            "userinfo" => $user->getInfoUser($infoAcco['id_seller'])
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
    if($session->isConnected()) {
        echo $twig->render("host.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être connecté.";
        header('Location: /');
    }
}

//====================== fin Partie HOST ======================



//====================== DEBUT Partie NEW PASS ======================
//http://localhost/new-pass/'.urlencode($id).'

elseif($page == "list-detail") {
    $where =$people= 0;

    if(isset($_GET['pl']) || isset($_GET['pe'])){
        if(isset($_GET['pl'])){$where = $_GET['pl'];}else{$where=0;}
        if(isset($_GET['pe'])){$people = $_GET['pe'];}else{$people=0;}
    }
    $search = new Form();
    $list = $search->getList($where, $people);
    require_once "../App/setCursorMaps.php";

    echo $twig->render("list-detail.twig", [
        "errors" => $session->getMessage("errors"),
        "id" => $parameter,
        "accolist" => $list,
        "values" => $utils->getFieldsValue()
    ]);
}
//====================== FIN Partie NEW PASS ======================

//====================== DEBUT Partie RESERVE ======================
elseif($page == "reserve"){
    if($session->isConnected() && $parameter){
        $acco = new Accomodation();
        echo $twig->render("reserve.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
            "values" => $utils->getFieldsValue(),
            "userinfo" => $user->getInfoUser($_SESSION['id']),
            "accoinfo" => $acco->getAccomodationById($parameter)
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être connecté.";
        header('Location: /');
    }
}

//====================== fin Partie HOST ======================



//====================== DEBUT Partie help ======================

elseif($page == "help") {
    if(isset($_POST['email']) && isset($_POST['object']) && isset($_POST['message']) && isset($_POST['captcha'])){
        $form = new Form;
        $form->sendMessageHelp($_POST['email'],$_POST['object'],$_POST['message'], $_POST['captcha']);
    }

    echo $twig->render("help.twig", [
        "values" => $utils->getFieldsValue(),
        "errors" => $session->getMessage("errors"),
        "success" => $session->getMessage("success")
    ]);
}
//====================== FIN Partie help ======================

//====================== DEBUT Partie maps ======================

elseif($page == "maps") {
    echo $twig->render("maps.twig");
}
//====================== FIN Partie maps ======================


//====================== DEBUT Partie ACCUEIL ======================
else{
    echo $twig->render("home.twig", [
        "accomodations_random" => $accomodationList->getRandom(10),
        "accomodations_top" => $accomodationList->getTop(6),
        "errors" => $session->getMessage("errors"),
        "success" => $session->getMessage("success"),
    ]);
}

//====================== FIN Partie ACCUEIL ======================

