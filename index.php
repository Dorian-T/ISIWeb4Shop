<?php
require_once 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/Product_controller.php';
require_once 'controller/Home_controller.php';
require_once 'controller/Login_controller.php';
require_once 'controller/Admin_controller.php';

session_start();
$homeController = new Home_controller();
$productController = new Product_controller();
$loginController = new Login_controller();
$adminController = new Admin_controller();

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
        case 'registered':
            $loader = new Twig\Loader\FilesystemLoader('view');
            $twig = new Twig\Environment($loader);
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
        default:
            header('Location: ./');
    }
} else {
    $homeController->afficherHome();
}
