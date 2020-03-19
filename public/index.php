<?php
//var_dump($_POST);
require_once "../vendor/autoload.php";
require_once "../App/functions.php";
use App\AccomodationList;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new App\Router($_GET['url']);


$loader = new FilesystemLoader('../App/views/page/');
$twig = new Environment($loader, [
    'cache' => false //'../tmp',
]);

$accomodationList = new AccomodationList();

$router->get("/", function() use ($accomodationList, $twig) {
    echo $twig->render("home.twig", [
        "accomodations_top" => $accomodationList->getTop(10),
        "accomodations_random" => $accomodationList->getRandom(10)

    ]);
});


$router->get("/login", function() use ($twig) {
    echo $twig->render("login.twig", [
        "errors" => getErrors(),
        "valuefield" => getFieldsValue()
    ]);
});

$router->get("/register", function() use ($twig) {
    echo $twig->render("register.twig");
});
$router->run();
