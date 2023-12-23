<?php

class Admin_controller {
    
    private $admin_model;
    private $user_model;
    
    public function __construct() {
        $this->admin_model = new admin_model();
        $this->user_model = new user_model();
    }

    function GenerateCommand() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->user_model->getAdmin(intval($_SESSION['admin_id'])) : null;
        $orderAdmin = $this->admin_model->getAllOrders();
        $order = $this->admin_model->getOrder();
        var_dump($_POST);
        if (!(empty($_POST))) {
            foreach ($orderAdmin as $i) {
                if ($_POST['id'] == $i['id']) {
                    $this->admin_model->updateOrderAdmin($_POST['id'], 10);
                }
            }
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('adminC.twig');
        echo $template->render(array('order' => $order, 'orderAdmin' => $orderAdmin, 'admin' => $admin));
    }

    function GenerateProduct() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->user_model->getAdmin(intval($_SESSION['admin_id'])) : null;
        $orderAdmin = $this->admin_model->getAllProducts();

        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('adminP.twig');
        echo $template->render(array('orderAdmin' => $orderAdmin, 'admin' => $admin));
    }

    function UpdateProduct() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->user_model->getAdmin(intval($_SESSION['admin_id'])) : null;
        $productId = isset($_POST['id']) ? intval($_POST['id']) : null;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    
        $this->admin_model->updateProduct($productId, $name, $description, $price, $quantity);
    
        // Rediriger vers la page de gestion des produits après la mise à jour
        header('Location: index.php?action=adminP');
    }

    function EditProduct() {
        $admin = (isset($_SESSION['admin_id'])) ? $this->user_model->getAdmin(intval($_SESSION['admin_id'])) : null;
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $product = $this->admin_model->getProduct($productId);
    
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);
    
        $template = $twig->load('editProduct.twig');
        echo $template->render(array('product' => $product, 'admin' => $admin));
    }

    public function DeleteProduct() {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        // Vérifiez si l'ID du produit est valide avant de supprimer
        if ($productId) {
            // Appelez la méthode dans votre modèle pour supprimer le produit
            $this->admin_model->deleteProduct($productId);
        }
    
        // Redirigez vers la page de gestion des produits après la suppression
        header('Location: index.php?action=adminP');
    }
    
}
?>