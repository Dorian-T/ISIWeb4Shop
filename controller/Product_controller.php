<?php

class Product_controller {

  private $produits_modele;

  public function __construct() {
    $this->produits_modele = new Produits_modele();
  }

  public function afficherProduitsByCategory() {
    $getCategory = $this->produits_modele->getCategory();

    $products = [];
    if (isset($_GET['category'])) {
        $products = $this->produits_modele->getProductsByCategory($_GET['category'])->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $products = $this->produits_modele->req_products();
    }

    $loader = new Twig\Loader\FilesystemLoader('view');
    $twig = new Twig\Environment($loader);

    // Charge le template 'Produits.html.twig'
    $template = $twig->load('produits.twig');
    echo $template->render(array('products' => $products, 'categories' => $getCategory));
  }

  public function productDetails($id) {
    
      $product = $this->produits_modele->getProductById($id);
      $reviews = $this->produits_modele->getReviewsByProductId($id);
  
      $loader = new Twig\Loader\FilesystemLoader('view');
      $twig = new Twig\Environment($loader);
  
      $template = $twig->load('productDetails.twig');
      echo $template->render(array('product' => $product, 'reviews' => $reviews));
  }

}

?>
