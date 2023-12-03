<?php

class Payement_controller {

    private $payement_model;

    public function __construct() {
        $this->payement_model = new Payement_model();
    }

    function AddressLivraison() {

        if (isset($_SESSION['customer_id'])) {
            $address = $this->address_model->getAddress($_SESSION['customer_id']);
        }
        if (!empty($_POST)) {
            $_SESSION['statut'] = 1;
            $this->deleteDeliveryAddress();

            if (isset($_POST['nouvadresse']) || !(isset($_SESSION['customer_id']))) {
                $_SESSION['forname'] = $_POST['forname'];
                $_SESSION['surname'] = $_POST['surname'];

                $this->address_model->addDeliveryAdress($_POST['forname'], $_POST['surname'], $_POST['add1'],$_POST['add2'], $_POST['city'], $_POST['postcode'],'','');
                foreach ($this->address_model->getDeliveryAddress() as $i) {
                    $_SESSION['adresse_id'] = $i['id'];
                }
                
                $this->adress_model->updateOrder($_SESSION['SESS_ORDERNUM'], 0, 0, $_SESSION['adresse_id'], 0, 1, $_SESSION['total'], session_id());
            }

            if (!(isset($_POST['nouvadresse'])) || !(isset($_SESSION['customer_id']))) {
                foreach ($Adresse as $i){
                    if ($_POST['SelectAdress'] == $i['id']) {
                        $add1 = $i['add1'];
                        $city = $i['city'];
                        $postcode = $i['postcode'];
                    }
                }

                $this->address_model->addDeliveryAdress($_SESSION['forname'], $_SESSION['surname'], $add1,'', $city, $postcode,'','');
                foreach ($this->address_model->getDeliveryAddress() as $i) {
                    $_SESSION['adresse_id'] = $i['id'];
                }

                $this->adress_model->updateOrder($_SESSION['SESS_ORDERNUM'], $_SESSION['customer_id'], 1, $_SESSION['adresse_id'], 0, 1, $_SESSION['total'], '');
            }
            header('Location: index.php?action=payement');
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('livraison.twig');
        echo $template->render(array());
    }
    
    function Payement() {

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
        echo $template->render(array());
    }

}


?>