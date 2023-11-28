<?php

require 'vendor/autoload.php';
require_once 'models/model.php';
require_once 'controleurs/Produits_controleur.php';
require_once 'controleurs/Home_controleur.php';

$homeController = new Home_controller();
$productController = new Produits_controller();

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