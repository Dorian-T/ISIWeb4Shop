<?php

/**
 * Class PayementController
 *
 * This class is responsible for handling the payement page functionality.
 */
class PayementController {

    /**
     * The payement model instance.
     */
    private $payementModel;

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;

    /**
     * PayementController constructor.
     */
    public function __construct($twig) {
        $this->payementModel = new PayementModel();
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    /**
     * Display the payement page.
     * 
     * @return void
     */
    public function payement() {

        $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;

        if (!empty($_POST)) {
            $_SESSION['statut'] = 2;
            if(isset($_SESSION['customer_id'], $_SESSION['SESS_ORDERNUM'], $_SESSION['adresse_id'], $_SESSION['total'])){
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], $_SESSION['customer_id'], 1, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], '');
            }
            elseif(isset($_SESSION['SESS_ORDERNUM'], $_SESSION['adresse_id'], $_SESSION['total'])){
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], 0, 0, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], session_id());
            }

            switch ($_POST['payement']) {
                case 'paypal':
                    header('Location: https://www.paypal.com/fr/home');
                    break;
                case 'cb':
                    $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;
                    $loader = new Twig\Loader\FilesystemLoader('view');
                    $twig = new Twig\Environment($loader);
                    $template = $twig->load('cb.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                    break;
                case 'cheque':
                    header('Location: facturePDF.php');
                    break;
                case 'virement':
                    $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;
                    $loader = new Twig\Loader\FilesystemLoader('view');
                    $twig = new Twig\Environment($loader);
                    $template = $twig->load('virement.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                    break;
                default:
                    header('Location: index.php?action=home');
                    break;
            }
        }

        $template = $this->twig->load('payement.twig');
        echo $template->render(array('customer' => $customer));
    }

    public function thankYou() {
        // Chargez le template de remerciement avec les détails de la commande
        $template = $this->twig->load('thankyou.twig');
        echo $template->render(array());
    }

}


?>