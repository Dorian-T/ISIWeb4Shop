<?php

class Payement_controller {

    private $payement_model;

    public function __construct() {
        $this->payement_model = new Payement_model();
    }

    function Payement() {

        if (!empty($_POST)) {
            $_SESSION['statut'] = 2;
            if(isset($_SESSION['customer_id'])){
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], $_SESSION['customer_id'], 1, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], '');
            }
            else{
                $this->payement_model->updateOrder($_SESSION['SESS_ORDERNUM'], 0, 0, $_SESSION['adresse_id'], 1, 2, $_SESSION['total'], session_id());
            }

            switch ($_POST['payement']) {
                case 'paypal':
                    header('Location: https://www.paypal.com/fr/home');
                    break;
                case 'card':
                    header('Location: index.php?action=card');
                    break;
                case 'cheque':
                    header('Location: facturePDF.php');
                    break;
                case 'virement':
                    header('Location: index.php?action=virement');
                    break;
                default:
                    header('Location: index.php?action=home');
                    break;
            } 
        }

        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('payement.twig');
        echo $template->render(array());
    }

}


?>