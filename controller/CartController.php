<?php

/**
 * Class CartController
 *
 * This class is responsible for handling the cart page functionality.
 */
class CartController {

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;

    private $productModel;

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * CartController constructor.
     *
     * @param $twig The Twig instance used for rendering templates.
     */
    public function __construct($twig) {
        $this->twig = $twig;
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display the cart page.
     * @return void
     */
    public function print(): void {
        $customer = (isset($_SESSION['customer_id']))
            ? $this->userModel->getCustomer(intval($_SESSION['customer_id']))
            : null;
        if(isset($_POST['idToDelete'])) {
            $this->productModel->removeProduct(
                $_POST['idToDelete'],
                $_POST['quantityToDelete'],
                $_POST['totalToDelete'],
                session_id(),
                $_SESSION['customer_id'] ?? -1
            );
        }

        $cart = $this->productModel->getCart(session_id(), $_SESSION['customer_id'] ?? -1);

        $template = $this->twig->load('cart.twig');
        echo $template->render(['cart' => $cart, 'customer' => $customer]);
    }
}
