<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'fpdf/fpdf_custom.php';

require_once 'model/productModel.php';
require_once 'model/userModel.php';
require_once 'model/adminModel.php';
require_once 'model/payementModel.php';

require_once 'controller/ProductController.php';
require_once 'controller/HomeController.php';
require_once 'controller/LoginController.php';
require_once 'controller/AdminController.php';
require_once 'controller/CartController.php';
require_once 'controller/PayementController.php';
require_once 'controller/FactureController.php';


$loader = new Twig\Loader\FilesystemLoader('view');
$twig = new Twig\Environment($loader, ['debug' => true]);
$twig->addExtension(new Twig\Extension\DebugExtension()); // debug

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
        case 'logout':
            $loginController = new LoginController($twig);
            $loginController->logOut();
            break;
        case 'registered':
            $template = $twig->load('registered.twig');
            echo $template->render(array());
            break;

        // Admin

        case 'admin':
            if (isset($_GET['page'])) {
                $adminController = new AdminController($twig);
                if($_GET['page'] == 'register') {
                    $loginController = new LoginController($twig);
                    $loginController->registerAdmin();
                }
                elseif ($adminController->isAdmin()) {
                    switch ($_GET['page']) {
                        case 'orders':
                            $adminController->generateOrders();
                            break;

                        case 'products':
                            $adminController->generateProducts();
                            break;

                        case 'editProduct':
                            $adminController->editProduct();
                            break;

                        default:
                            header('Location: ./');
                    }
                }
            }
            else {
                header('Location: ./');
            }
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
            $livraisonController = new PayementController($twig);
            $livraisonController->deliveryAdress();
        break;
        case 'facture':
            $factureController = new FactureController($twig);
            $factureController->facture();
        break;
        default:
            header('Location: ./');
    }
}
else {
    $homeController = new HomeController($twig);
    $homeController->afficherHome();
}
