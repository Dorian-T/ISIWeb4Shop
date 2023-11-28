<?php

class Produits_controller {

  private $produits_modele;

  public function __construct() {
    $this->produits_modele = new Produits_modele();
  }

  public function afficherProduits() {
    $products = $this->produits_modele->req_products();

    $loader = new Twig\Loader\FilesystemLoader('view');
    $twig = new Twig\Environment($loader);

    // Charge le template 'Produits.html.twig'
    $template = $twig->load('Produits.twig');
    echo $template->render(array('products' => $products));
  }
}

?>
