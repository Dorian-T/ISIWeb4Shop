<?php

require 'vendor/autoload.php';
require_once 'model/model.php';
require_once 'controller/Product_controller.php';
require_once 'controller/Home_controller.php';

$homeController = new Home_controller();
$productController = new Product_controller();

if(isset($_GET['action'])) {
  switch ($_GET['action']) {
    case 'home':
        $homeController->afficherHome();
        break;
    case 'products':
        $productController->afficherProduits();
        break;    
    default:
            throw new Exception("Action non valide");
    }
} 

  
?>