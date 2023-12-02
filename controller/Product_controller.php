<?php

class Product_controller {

  private $produitsModele;

  public function __construct() {
    $this->produitsModele = new Produits_modele();
  }

  public function afficherProduitsByCategory() {
    $getCategory = $this->produitsModele->getCategory()->fetchAll(PDO::FETCH_ASSOC);
    $products = [];

    // Filtrage des produits par catégorie
    if (isset($_GET['category'])) {
      // Récupère les id des catégories
      $categoryId = array_map(function($category) {
        return $category['id'];
      }, $getCategory);

      if(array_search($_GET['category'], $categoryId) === false) {
        header('Location: index.php?action=products');
      }
      else {
        $products = $this->produitsModele->getProductsByCategory($_GET['category'])->fetchAll(PDO::FETCH_ASSOC);
      }
    }
    else {
        $products = $this->produitsModele->req_products();
    }

    $loader = new Twig\Loader\FilesystemLoader('view');
    $twig = new Twig\Environment($loader);

    // Charge le template 'Produits.html.twig'
    $template = $twig->load('produits.twig');
    echo $template->render(array('products' => $products, 'categories' => $getCategory));
  }

  public function productDetails($id) {
    
      $product = $this->produitsModele->getProductById($id);
      $reviews = $this->produitsModele->getReviewsByProductId($id);
  
      $loader = new Twig\Loader\FilesystemLoader('view');
      $twig = new Twig\Environment($loader);
  
      $template = $twig->load('productDetails.twig');
      echo $template->render(array('product' => $product, 'reviews' => $reviews));
  }
}
