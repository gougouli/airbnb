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
        echo $twig->render("form/login.twig",[
            "errors" => $session->getMessage("errors"),
            "values" => $utils->getFieldsValue()
        ]);
    }else{
        $_SESSION['errors'][] = "Vous êtes déjà connecté.";
        header("Location: /$_SESSION[page]");
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
        echo $twig->render("form/register.twig", [
            "errors" => $session->getMessage("errors"),
            "values" => $utils->getFieldsValue()
        ]);
    }else{
        $_SESSION['errors'][] = "Vous êtes déjà connecté.";
        header("Location: /$_SESSION[page]");
    }

}
elseif($page == "logout"){
    if($session->isConnected()) {
        $session->disconnect();
    }else{
        $_SESSION['errors'][] = "Vous n'êtes pas connecté.";
        header("Location: /$_SESSION[page]");
    }
}
//====================== FIN Partie Connexion / Incription / Deconnexion ======================

//====================== DEBUT Partie ACCOUNT ======================
elseif($page == "account"){
    if($session->isConnected()) {
        echo $twig->render("connected/account.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
            "info" => $user->getInfoUser($_SESSION['id'])
        ]);
    }else{
        $_SESSION['errors'][] = "Vous n'êtes pas connecté.";
        header("Location: /$_SESSION[page]");
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
        echo $twig->render("form/forgot-pass.twig",[
            "errors" => $session->getMessage("errors")
        ]);

    }else{
        $_SESSION['errors'][] = "Vous devez vous déconnecter.";
        header("Location: /$_SESSION[page]");
    }
}

//====================== fin Partie FORGOT PASS ======================

//====================== DEBUT Partie NEW PASS ======================

elseif($page == "new-pass") {
    if (!$session->isConnected() && $parameter) {
        if (!empty($_POST['pass']) && !empty($_POST['repass'])) {
            $pass = $_POST['pass'];
            $repass = $_POST['repass'];
            $id = $_POST['idtoken'];
            $form = new Form();
            $form->newpass($id, $pass, $repass);
        }
        echo $twig->render("form/new-pass.twig", [
            "errors" => $session->getMessage("errors"),
            "id" => $parameter
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être déconnecté.";
        header("Location: /$_SESSION[page]");
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
        header("location:/$_SESSION[page]");
    }
}
//====================== FIN Partie detail ======================


//====================== DEBUT Partie HOST ======================
elseif($page == "host"){
    require_once "../App/create_acco.php";
    if($session->isConnected()) {
        echo $twig->render("connected/host.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être connecté.";
        header("Location: /$_SESSION[page]");
    }
}

//====================== fin Partie HOST ======================



//====================== DEBUT Partie NEW PASS ======================
//http://localhost/new-pass/'.urlencode($id).'

elseif($page == "list-detail") {
    if(isset($_GET['pl']) && isset($_GET['pe']) && isset($_GET['prmi']) && isset($_GET['prma']) && isset($_GET['da']) && isset($_GET['dr'])){
        $search = new Form();
        $list = $search->Search($_GET['prmi'], $_GET['prma'], $_GET['pl'], $_GET['pe'], $_GET['da'], $_GET['dr']);
    }else{
        $accolist = new AccomodationList();
        $list = $accolist->getAll();
    }
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
        echo $twig->render("connected/reserve.twig",[
            "errors" => $session->getMessage("errors"),
            "success" => $session->getMessage("success"),
            "values" => $utils->getFieldsValue(),
            "userinfo" => $user->getInfoUser($_SESSION['id']),
            "accoinfo" => $acco->getAccomodationById($parameter)
        ]);
    }else{
        $_SESSION['errors'][] = "Vous devez être connecté.";
        header("Location: /$_SESSION[page]");
    }
}

//====================== fin Partie HOST ======================



//====================== DEBUT Partie help ======================

elseif($page == "help") {
    if(isset($_POST['email']) && isset($_POST['object']) && isset($_POST['message']) && isset($_POST['captcha'])){
        $form = new Form;
        $form->sendMessageHelp($_POST['email'],$_POST['object'],$_POST['message'], $_POST['captcha']);
    }

    echo $twig->render("form/help.twig", [
        "values" => $utils->getFieldsValue(),
        "errors" => $session->getMessage("errors"),
        "success" => $session->getMessage("success")
    ]);
}
//====================== FIN Partie help ======================

//====================== DEBUT Partie maps ======================

elseif($page == "maps") {
    echo $twig->render("annexe/maps.twig");
}
//====================== FIN Partie maps ======================


//====================== DEBUT Partie A propos ======================

elseif($page == "about-us") {
    echo $twig->render("annexe/aboutus.twig");
}
//====================== FIN Partie A propos ======================


//====================== DEBUT Partie CONTRIB ======================

elseif($page == "contrib") {
    echo $twig->render("annexe/contrib.twig");
}
//====================== FIN Partie CONTRIBs ======================

//====================== DEBUT Partie Politiques ======================

elseif($page == "politics") {
    echo $twig->render("annexe/politics.twig");
}
//====================== FIN Partie Politiques ======================


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

$_SESSION['page'] = $page;
