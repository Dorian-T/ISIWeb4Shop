<?php

/**
 * Class ProductController
 *
 * This class is responsible for handling the product page functionality.
 */
class ProductController {

    /**
     * The Twig instance used for rendering views.
     */
    private $twig;

    /**
     * The product model instance.
     */
    private $productModel;

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * ProductController constructor.
     *
     * @param $twig The Twig instance used for rendering views.
     */
    public function __construct($twig) {
        $this->twig = $twig;
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();

        // Supprime les paniers inutilisés
        $this->productModel->removeUnusedCarts();
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
            $this->addTocart($_POST['id'], $_POST['quantity'] ?? 1);
            header('Location: index.php?action=products');
        }
        elseif(isset($_POST['productId'])) {
            $this->addReview($_POST['productId'], $_POST['name'], $_POST['stars'], $_POST['title'], $_POST['description']);
            header('Location: index.php?action=products&id=' . $_POST['productId']);
        }
        elseif ($id != -1) {
            $this->productDetails($id);
        }
        else {
            $this->products();
        }
    }

    /**
     * Retrieves the list of products.
     *
     * @return void
     */
    public function products(): void {
        $customer = (isset($_SESSION['customer_id']))
            ? $this->userModel->getCustomer(intval($_SESSION['customer_id']))
            : null;
        $admin = (isset($_SESSION['admin_id']))
            ? $this->userModel->getAdminUsername(intval($_SESSION['admin_id']))
            : null;

        $getCategory = $this->productModel->getCategory()->fetchAll(PDO::FETCH_ASSOC);
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
                $products = $this->productModel->getProductsByCategory($_GET['category'])->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $products = $this->productModel->getProducts();
        }

        $template = $this->twig->load('products.twig');
        echo $template->render(array('products' => $products, 'categories' => $getCategory, 'customer' => $customer, 'admin' => $admin));
    }

    /**
     * Retrieves the details of a product.
     *
     * @param int $id The ID of the product.
     * @return void
     */
    public function productDetails($id): void {
        $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;
        $admin = (isset($_SESSION['admin_id'])) ? $this->userModel->getAdminUsername(intval($_SESSION['admin_id'])) : null;
        $product = $this->productModel->getProductById($id);
        $reviews = $this->productModel->getReviewsByProductId($id);

        $template = $this->twig->load('productDetails.twig');
        echo $template->render(array('product' => $product, 'reviews' => $reviews, 'customer' => $customer, 'admin' => $admin));
    }

    /**
     * Adds a review to a product.
     *
     * @return void
     */
    public function addReview(int $productId, string $name, int $stars, string $title, string $description): void {
        // Vérification des données du formulaire
        $stars = ($stars < 1) ? 1 : $stars;
        $stars = ($stars > 5) ? 5 : $stars;

        // Ajoutez le commentaire à la base de données
        $this->productModel->addComment($productId, $name, $stars, $title, $description);
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
    public function addTocart($id, $quantity): void {
        $product = $this->productModel->getProductById($id);

        // Le produit existe et est en stock
        if ($product != null && $product['quantity'] > $quantity) {

            // Le client est connecté
            if (isset($_SESSION['customer_id'])) {
                $cartId = $this->productModel->getCartIdByCustomerId($_SESSION['customer_id']);

                // Le panier existe
                if($cartId != -1) {
                    $this->productModel->addProductToCart($cartId, $product, $quantity);
                }
                // Le panier n'existe pas
                else {
                    $this->productModel->createCart(session_id(), $_SESSION['customer_id']);
                    $cartId = $this->productModel->getCartIdByCustomerId($_SESSION['customer_id']);
                    $this->productModel->addProductToCart($cartId, $product, $quantity);
                }
            }
            // Le client n'est pas connecté
            else {
                $cartId = $this->productModel->getCartIdBySessionId(session_id());

                // Le panier existe
                if($cartId != -1) {
                    $this->productModel->addProductToCart($cartId, $product, $quantity);
                }
                // Le panier n'existe pas
                else {
                    $this->productModel->createCart(session_id());
                    $cartId = $this->productModel->getCartIdBySessionId(session_id());
                    $this->productModel->addProductToCart($cartId, $product, $quantity);
                }
            }
        }
    }
}
