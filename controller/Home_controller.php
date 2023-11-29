<?php 

class Home_controller {

  private $home_modele;

  public function __construct() {
  }

  public function afficherHome() {
    $loader = new Twig\Loader\FilesystemLoader('view');
    $twig = new Twig\Environment($loader);

    // Charge le template 'Home.html.twig'
    $template = $twig->load('home.twig');
    echo $template->render(array());
  }
}

?>