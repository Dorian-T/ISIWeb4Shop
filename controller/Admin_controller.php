<?php

class Admin_controller {
    
    private $admin_model;
    
    public function __construct() {
        $this->admin_model = new admin_model();
    }

    function GenerateCommand() {
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
        echo $template->render(array('order' => $order, 'orderAdmin' => $orderAdmin));
    }

    function GenerateProduct() {
        $orderAdmin = $this->admin_model->getAllProducts();

        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('adminP.twig');
        echo $template->render(array('orderAdmin' => $orderAdmin));
    }

    function UpdateProduct() {
        $orderAdmin = $this->admin_model->getAllProducts();
        if (!(empty($_POST))) {
            foreach ($orderAdmin as $i) {
                if ($_POST['id'] == $i['id']) {
                    $name = isset($_POST['name']) ? $_POST['name'] : '';
                    $description = isset($_POST['description']) ? $_POST['description'] : '';
                    $price = isset($_POST['price']) ? $_POST['price'] : '';
                    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
                    $cat_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : '';
    
                    $this->admin_model->updateProduct($_POST['id'], $name, $description, $price, $quantity, $cat_id);
                }
            }
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);
    
        $template = $twig->load('adminP.twig');
        echo $template->render(array('orderAdmin' => $orderAdmin));
    }

}
?>