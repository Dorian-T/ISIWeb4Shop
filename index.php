<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/ProductController.php';
require_once 'controller/Home_controller.php';
require_once 'controller/Login_controller.php';
require_once 'controller/Admin_controller.php';
require_once 'controller/CartController.php';

$loader = new Twig\Loader\FilesystemLoader('view');
$twig = new Twig\Environment($loader);

$loginController = new Login_controller();
$adminController = new Admin_controller();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'products':
            $productController = new ProductController($twig);
            if(isset($_GET['id'])) {
                $productController->print($_GET['id']);
            }
            else {
                $productController->print(null);
            }
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

        case 'registered':
            $template = $twig->load('registered.twig');
            echo $template->render(array());
            exit();
            break;

        case 'adminP':
            $adminController->GenerateProduct();
            break;

        case 'adminC':
            $adminController->GenerateCommand();
            break;

        case 'cart':
            $cartController = new CartController($twig);
            $cartController->print();
            break;

        default:
            header('Location: ./');
    }
}
else {
    session_destroy();
    $homeController = new Home_controller();
    $homeController->afficherHome();
}
