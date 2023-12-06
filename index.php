<?php

require_once 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/ProductController.php';
require_once 'controller/Home_controller.php';
require_once 'controller/Login_controller.php';
require_once 'controller/Admin_controller.php';
require_once 'controller/CartController.php';

session_start();

$loader = new Twig\Loader\FilesystemLoader('view'); // TODO : passer twig en variable dans chaque constructeur
$twig = new Twig\Environment($loader);

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
            $loginController = new Login_controller();
            $loginController->logIn();
            break;
        case 'register':
            $loginController = new Login_controller();
            $loginController->register();
            break;
        case 'logout':
            $loginController = new Login_controller();
            $loginController->logOut();
            break;

        case 'registered':
            $template = $twig->load('registered.twig');
            echo $template->render(array());
            exit();
            break;

        case 'adminP':
            $adminController = new Admin_controller();
            $adminController->GenerateProduct();
            break;

        case 'adminC':
            $adminController = new Admin_controller();
            $adminController->GenerateCommand();
            break;

        case 'cart':
            $cartController = new CartController($twig);
            $cartController->print();
            break;

        case 'payement':
            $payementController->Payement();
            break;
        default:
            header('Location: ./');
    }
}
else {
    $homeController = new Home_controller();
    $homeController->afficherHome();
}
