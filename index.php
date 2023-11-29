<?php

require_once 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/Product_controller.php';
require_once 'controller/Home_controller.php';
require_once 'controller/Login_controller.php';

$homeController = new Home_controller();
$productController = new Product_controller();
$loginController = new Login_controller();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'products':
            $productController->afficherProduitsByCategory();
            break;
        case 'login':
            $loginController->logIn();
            break;
        case 'logout':
            $loginController->logOut();
            break;
        case 'register':
            $loginController->register();
            break;
        default:
            header('Location: ./');
    }
} else {
    $homeController->afficherHome();
}
