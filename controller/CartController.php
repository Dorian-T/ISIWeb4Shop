<?php

class CartController {
    private $twig;

    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function print() {
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('cart.twig');
        echo $template->render(array());
    }
}
