<?php

/**
 * Class HomeController
 *
 * This class is responsible for handling the home page functionality.
 */
class HomeController {
    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;

    /**
     * HomeController constructor.
     *
     * @param $twig The Twig instance used for rendering templates.
     */
    public function __construct($twig) {
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    /**
     * Display the home page.
     */
    public function afficherHome() {
        $customer = (isset($_SESSION['customer_id']))
            ? $this->userModel->getCustomer(intval($_SESSION['customer_id']))
            : null;
        $admin = (isset($_SESSION['admin_id']))
            ? $this->userModel->getAdminUsername(intval($_SESSION['admin_id']))
            : null;

        // Charge le template 'Home.html.twig'
        $template = $this->twig->load('home.twig');
        echo $template->render(array('customer' => $customer, 'admin' => $admin));
    }
}
