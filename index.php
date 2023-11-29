<?php

require_once 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/Product_controller.php';
require_once 'controller/Home_controller.php';

$homeController = new Home_controller();
$productController = new Product_controller();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'products':
            $productController->afficherProduitsByCategory();
            break;
        default:
            header('Location: ./');
    }
} else {
    $homeController->afficherHome();
}
