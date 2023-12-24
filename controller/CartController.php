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

    /**
     * CartController constructor.
     *
     * @param $twig The Twig instance used for rendering templates.
     */
    public function __construct($twig) {
        $this->twig = $twig;
    }

    /**
     * Display the cart page.
     */
    public function print() {
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);
    
        $template = $twig->load('cart.twig');
        echo $template->render(['cart' => isset($_SESSION['cart']) ? $_SESSION['cart'] : []]);
    }
}

?>
