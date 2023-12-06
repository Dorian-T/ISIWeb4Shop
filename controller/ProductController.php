<?php

class ProductController {

    private $twig;

    private $produitsModele;

    /**
     * ProductController constructor.
     *
     * @param $twig The Twig instance used for rendering views.
     */
    public function __construct($twig) {
        $this->twig = $twig;
        $this->produitsModele = new Produits_modele();
    }

    /**
     * Prints the product with the given ID.
     * If no ID is given, prints the list of products.
     *
     * @param int $id The ID of the product to print.
     * @return void
     */
    public function print($id): void {
        if(isset($_POST['id'])) {
            $this->addTocart($_POST['id']);
        }
        if (isset($id)) {
            $this->productDetails($id);
        } else {
            $this->products();
        }
    }

    /**
     * Retrieves the list of products.
     *
     * @return void
     */
    public function products(): void {
        $getCategory = $this->produitsModele->getCategory()->fetchAll(PDO::FETCH_ASSOC);
        $products = [];

        // Filtrage des produits par catégorie
        if (isset($_GET['category'])) {
            // Récupère les id des catégories
            $categoryId = array_map(function ($category) {
                return $category['id'];
            }, $getCategory);

            if (array_search($_GET['category'], $categoryId) === false) {
                header('Location: index.php?action=products');
            } else {
                $products = $this->produitsModele->getProductsByCategory($_GET['category'])->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $products = $this->produitsModele->req_products();
        }

        $template = $this->twig->load('products.twig');
        echo $template->render(array('products' => $products, 'categories' => $getCategory));
    }

    /**
     * Retrieves the details of a product.
     *
     * @param int $id The ID of the product.
     * @return void
     */
    public function productDetails($id): void {
        $product = $this->produitsModele->getProductById($id);
        $reviews = $this->produitsModele->getReviewsByProductId($id);

        $template = $this->twig->load('productDetails.twig');
        echo $template->render(array('product' => $product, 'reviews' => $reviews));
    }

    /**
     * Adds a product to the cart.
     * If the product doesn't exist or is out of stock, does nothing.
     * If the cart doesn't exist, creates it.
     * If the product is already in the cart, increments the quantity.
     *
     * @param int $id The ID of the product to add.
     * @return void
     */
    public function addTocart($id): void {
        // Vérifie si le produit existe dans la base de données
        $product = $this->produitsModele->getProductById($id);
        if($product != null && $product['quantity'] > 0) {
            // Ajoute le produit au panier dans la session
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]++;
            } else {
                $_SESSION['cart'][$id] = 1;
            }

            // Ajoute le produit au panier dans la base de données
            $this->produitsModele->addProductToCart($_SESSION['user']['id'] ?? null, session_id(), $product, 1);
        }
    }
}
