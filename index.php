<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'model/product_model.php';
require_once 'model/user_model.php';
require_once 'model/admin_model.php';
require_once 'model/payement_model.php';
require_once 'model/facture_model.php';
require_once 'model/livraison_model.php';
require_once 'controller/ProductController.php';
require_once 'controller/HomeController.php';
require_once 'controller/LoginController.php';
require_once 'controller/Admin_controller.php';
require_once 'controller/CartController.php';
require_once 'controller/Payement_controller.php';
require_once 'controller/LivraisonController.php';

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
            $adminController = new Admin_controller();
            $adminController->GenerateProduct();
            break;

        case 'updateProduct':
            $adminController = new Admin_controller();
            $adminController->UpdateProduct();
            break;

        case 'editProduct':
            $adminController = new Admin_controller();
            $adminController->EditProduct();
            break;

        case 'deleteProduct':
            $adminController = new Admin_controller();
            $adminController->DeleteProduct();
            break;
            
        case 'adminC':
            $adminController = new Admin_controller();
            $adminController->GenerateCommand();
            break;

        case 'cart':
            $cartController = new CartController($twig);
            $cartController->print();
            break;
        
        case 'removeFromCart':
            $productController = new ProductController($twig);
            if (isset($_POST['id'])) {
                $productController->removeProductFromCart($_POST['id']);
            }
            // Redirigez l'utilisateur vers la page du panier après la suppression
            header('Location: index.php?action=cart');
            break;

        case 'payement':
            $payementController = new Payement_controller();
            $payementController->Payement();
            break;
        case 'livraison':
            $livraisonController = new LivraisonController($twig);
            $livraisonController->showDeliveryAddressOrForm();
        break;
        default:
            header('Location: ./');
    }
}
else {
    $homeController = new HomeController($twig);
    $homeController->afficherHome();
}
