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
     * function to display the admin page for the command
     */
    public function generateCommand() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->userModel->getAdmin(intval($_SESSION['admin_id'])) : null;
        $orderAdmin = $this->adminModel->getAllOrders();
        $order = $this->adminModel->getOrder();
        var_dump($_POST);
        if (!(empty($_POST))) {
            foreach ($orderAdmin as $i) {
                if ($_POST['id'] == $i['id']) {
                    $this->adminModel->updateOrderAdmin($_POST['id'], 10);
                }
            }
        }

        $template = $this->twig->load('adminC.twig');
        echo $template->render(array('order' => $order, 'orderAdmin' => $orderAdmin, 'admin' => $admin));
    }

    /**
     * function to display the admin page for the product
     */
    public function generateProduct() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->userModel->getAdmin(intval($_SESSION['admin_id'])) : null;
        $orderAdmin = $this->adminModel->getAllProducts();

        $template = $this->twig->load('adminP.twig');
        echo $template->render(array('orderAdmin' => $orderAdmin, 'admin' => $admin));
    }

    public function updateProduct() {
        $productId = isset($_POST['id']) ? intval($_POST['id']) : null;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    
        $this->adminModel->updateProduct($productId, $name, $description, $price, $quantity);
    
        // Rediriger vers la page de gestion des produits après la mise à jour
        header('Location: index.php?action=adminP');
    }

    /**
     * function to modify a product
     */
    public function editProduct() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->userModel->getAdmin(intval($_SESSION['admin_id'])) : null;
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $product = $this->adminModel->getProduct($productId);
    
        $template = $this->twig->load('editProduct.twig');
        echo $template->render(array('product' => $product, 'admin' => $admin));
    }

    /**
     * function to delete a product
     */
    public function deleteProduct() {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        // Vérifiez si l'ID du produit est valide avant de supprimer
        if ($productId) {
            // Appelez la méthode dans votre modèle pour supprimer le produit
            $this->adminModel->deleteProduct($productId);
        }
    
        // Redirigez vers la page de gestion des produits après la suppression
        header('Location: index.php?action=adminP');
    }
    
}

?>