<?php

class Payement_controller {

    private $payement_model;
    private $user_model;

    public function __construct() {
        $this->payement_model = new Payement_model();
    }

    function Payement() {

        $customer = (isset($_SESSION['customer_id'])) ? $this->user_model->getCustomer(intval($_SESSION['customer_id'])) : null;

        if (!empty($_POST)) {
            $_SESSION['statut'] = 2;
            if(isset($_SESSION['customer_id'], $_SESSION['SESS_ORDERNUM'], $_SESSION['adresse_id'], $_SESSION['total'])){
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], $_SESSION['customer_id'], 1, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], '');
            }
            else if(isset($_SESSION['SESS_ORDERNUM'], $_SESSION['adresse_id'], $_SESSION['total'])){
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], 0, 0, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], session_id());
            }

            switch ($_POST['payement']) {
                case 'paypal':
                    header('Location: https://www.paypal.com/fr/home');
                    break;
                case 'cb':
                    $loader = new Twig\Loader\FilesystemLoader('view');
                    $twig = new Twig\Environment($loader);
                    $template = $twig->load('cb.twig');
                    echo $template->render(array());
                    exit();
                    break;
                case 'cheque':
                    header('Location: facturePDF.php');
                    break;
                case 'virement':
                    $loader = new Twig\Loader\FilesystemLoader('view');
                    $twig = new Twig\Environment($loader);
                    $template = $twig->load('virement.twig');
                    echo $template->render(array());
                    exit();
                    break;
                default:
                    header('Location: index.php?action=home');
                    break;
            } 
        }

        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('payement.twig');
        echo $template->render(array('customer' => $customer));
    }

}


?>