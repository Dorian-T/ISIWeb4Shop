<?php

// Incluez l'autoloader de Composer s'il est utilisé
//require 'vendor/autoload.php';

// Chargez votre configuration Twig
// $loader = new \Twig\Loader\FilesystemLoader('/chemin/vers/vos/templates');
// $twig = new \Twig\Environment($loader);

// Récupérez les produits depuis la base de données
$produits = req_products();

// Chargez le template Twig
echo $twig->render('produits.twig', ['produits' => $produits]);

?>
