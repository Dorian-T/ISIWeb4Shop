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
     * CartController constructor.
     *
     * @param $twig The Twig instance used for rendering templates.
     */
    public function __construct($twig) {
        $this->twig = $twig;
        $this->productModel = new ProductModel();
    }

    /**
     * Display the cart page.
     * @return void
     */
    public function print(): void {
        if(isset($_POST['productIdToDelete'])) {
            $this->productModel->removeProduct($_POST['productIdToDelete'], session_id(), $_SESSION['customer_id'] ?? null);
        }

        $cart = $this->productModel->getCart(session_id(), $_SESSION['customer_id'] ?? null);

        $template = $this->twig->load('cart.twig');
        echo $template->render(['cart' => $cart]);
    }
}
