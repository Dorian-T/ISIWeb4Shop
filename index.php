<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'model/productModel.php';
require_once 'model/userModel.php';
require_once 'model/adminModel.php';
require_once 'model/payementModel.php';
require_once 'model/factureModel.php';
require_once 'model/livraisonModel.php';
require_once 'model/factureModel.php';

require_once 'controller/ProductController.php';
require_once 'controller/HomeController.php';
require_once 'controller/LoginController.php';
require_once 'controller/AdminController.php';
require_once 'controller/CartController.php';
require_once 'controller/PayementController.php';
require_once 'controller/LivraisonController.php';
require_once 'controller/FactureController.php';


$loader = new Twig\Loader\FilesystemLoader('view');
$twig = new Twig\Environment($loader);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'products':
            $productController = new ProductController($twig);
            if(isset($_GET['id'])) {
                $productController->print($_GET['id']);
            }
            else {
                $productController->print(-1);
            }
            break;

        // Connexion et inscription
        case 'login':
            $loginController = new LoginController($twig);
            $loginController->logIn();
            break;
        case 'register':
            $loginController = new LoginController($twig);
            $loginController->register();
            break;
        case 'registerAdmin':
            $loginController = new LoginController($twig);
            $loginController->registerAdmin();
            break;
        case 'logout':
            $loginController = new LoginController($twig);
            $loginController->logOut();
            break;
        case 'registered':
            $template = $twig->load('registered.twig');
            echo $template->render(array());
            break;

        case 'adminP':
            $adminController = new AdminController($twig);
            $adminController->generateProduct();
            break;

        case 'updateProduct':
            $adminController = new AdminController($twig);
            $adminController->updateProduct();
            break;

        case 'editProduct':
            $adminController = new AdminController($twig);
            $adminController->editProduct();
            break;

        case 'deleteProduct':
            $adminController = new AdminController($twig);
            $adminController->deleteProduct();
            break;
            
        case 'adminC':
            $adminController = new AdminController($twig);
            $adminController->generateCommand();
            break;

        case 'cart':
            $cartController = new CartController($twig);
            $cartController->print();
            break;

        case 'payement':
            $payementController = new PayementController($twig);
            $payementController->payement();
            break;
        case 'thankyou':
            $payementController = new PayementController($twig);
            $payementController->thankYou();
            break;
        case 'livraison':
            $livraisonController = new LivraisonController($twig);
            $livraisonController->showDeliveryAddressOrForm();
        break;
        case 'facture':
            if (isset($_GET['id'])) {
                $factureController = new FactureController($twig);
                $factureController->facture($_GET['id']);
            }
            break;
        default:
            header('Location: ./');
    }
}
else {
    $homeController = new HomeController($twig);
    $homeController->afficherHome();
}
