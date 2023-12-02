<?php

class HomeController {
    public function afficherHome() {
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('home.twig');
        echo $template->render(array());
    }
}
