<?php
//var_dump($_POST);
require_once "../vendor/autoload.php";
require_once "../App/functions.php";
use App\AccomodationList;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$url = $_GET['url'];
$url = explode("/",trim($url, "/"));
$page = isset($url['0']) ? $url['0'] : "/";
$parameter = isset($url['1']) ? $url['1'] : "";

$loader = new FilesystemLoader('../App/views/page/');
$twig = new Environment($loader, [
    'cache' => false //'../tmp',
]);

$accomodationList = new AccomodationList();

if($page == "login"){
    echo $twig->render("login.twig",[
        "errors" => getErrors(),
        "values" => getFieldsValue()
    ]);
}elseif($page == "register"){
    echo $twig->render("register.twig",[
        "errors" => getErrors(),
        "values" => getFieldsValue()
    ]);
}
