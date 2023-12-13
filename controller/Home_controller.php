<?php 

class Home_controller {

  private $home_modele;
  private $user_model;

  public function __construct() {
    $this->user_model = new user_model();
  }

  public function afficherHome() {

    $loader = new Twig\Loader\FilesystemLoader('view');
    $twig = new Twig\Environment($loader);

    $customer = (isset($_SESSION['customer_id'])) ? $this->user_model->getCustomer(intval($_SESSION['customer_id'])) : null;
    $admin = (isset($_SESSION['admin_id'])) ? $this->user_model->getAdmin(intval($_SESSION['admin_id'])) : null;

    // Charge le template 'Home.html.twig'
    $template = $twig->load('home.twig');
    echo $template->render(array( 'customer' => $customer, 'admin' => $admin));
  }
}

?>