<?php

/**
 * Class AdminController
 *
 * This class is responsible for handling the admin page functionality.
 */
class AdminController {

    /**
     * The admin model instance.
     */
    private $adminModel;

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;
    
    /**
     * AdminController constructor.
     *
     * @param $twig
     */
    public function __construct($twig) {
        $this->adminModel = new AdminModel();
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool Returns true if the user is an admin, false otherwise.
     */
    public function isAdmin() {
        if(isset($_SESSION['admin_id'])) {
            return $this->adminModel->isAdmin($_SESSION['admin_id']);
        }
        return false;
    }

    /**
     * Display the admin page to validate orders.
     */
    public function generateOrders() {
        $order = $this->adminModel->getAllOrders();
        $username = $this->userModel->getAdminUsername($_SESSION['admin_id']);

        if(isset($_POST['id'])) {
            // Bouton de validation d'une commande
            if(isset($_POST['validate'])) {
                $this->adminModel->validateOrder($_POST['id']);
                $order = $this->adminModel->getAllOrders();
            }
            // Bouton de détails d'une commande
            elseif(isset($_POST['details'])) {
                $orderDetail = $this->adminModel->getOrderDetails($_POST['id']);
                $template = $this->twig->load('adminOrderDetails.twig');
                echo $template->render(
                    array('products' => $orderDetail[0], 'address' => $orderDetail[1], 'admin' => $username)
                );
                exit();
            }
        }

        $template = $this->twig->load('adminOrders.twig');
        echo $template->render(array('orders' => $order, 'admin' => $username));
    }

    /**
     * Display the admin page to manage products.
     */
    public function generateProducts() {
        $products = $this->adminModel->getAllProducts();
        $username = $this->userModel->getAdminUsername($_SESSION['admin_id']);

        // Modification d'un produit
        if(isset($_POST['id']) && isset($_POST['name'])) {
            $this->updateProduct();
            $products = $this->adminModel->getAllProducts();
        }
        // Bouton de suppression d'un produit
        if(isset($_POST['delete'])) {
            $this->adminModel->deleteProduct($_POST['delete']);
            $products = $this->adminModel->getAllProducts();
        }

        $template = $this->twig->load('adminProducts.twig');
        echo $template->render(array('products' => $products, 'admin' => $username));
    }

    /**
     * Display the admin page to modify a product.
     */
    public function editProduct() {
        if(isset($_GET['id'])) {
            $productId = intval($_GET['id']);
            $product = $this->adminModel->getProduct($productId);
            $admin = $this->userModel->getAdminUsername($_SESSION['admin_id']);

            $template = $this->twig->load('adminEditProduct.twig');
            echo $template->render(array('product' => $product, 'admin' => $admin));
        }
        else {
            header('Location: index.php?action=admin&page=products');
        }
    }

    /**
     * Updates a product.
     *
     * @access private
     * @return void
     */
    private function updateProduct() {
        $productId = isset($_POST['id']) ? intval($_POST['id']) : null;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    
        $this->adminModel->updateProduct($productId, $name, $description, $price, $quantity);
    }
}
