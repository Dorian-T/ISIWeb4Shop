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
     * Displays the delivery address of the logged-in user and allows the user to add a new address.
     *
     * @return void
     */
    public function deliveryAdress(): void {
        $customer = (isset($_SESSION['customer_id']))
            ? $this->userModel->getCustomer(intval($_SESSION['customer_id']))
            : null;
        $address = null;

        // Récupération de l'adresse du customer
        if (isset($_SESSION['customer_id'])) {
            $address = $this->payementModel->getAddressByCustomer($_SESSION['customer_id']);
        }

        // Formulaire pour ajouter une nouvelle adresse de livraison
        $template = $this->twig->load('delivery.twig');
        echo $template->render(array('delivery_address' => $address, 'customer' => $customer));
    }

    public function payement() {
        $customer = (isset($_SESSION['customer_id']))
            ? $this->userModel->getCustomer(intval($_SESSION['customer_id']))
            : null;

        // Ajout de l'adresse de livraison
        if(isset($_POST['newAdress'])) {
            $this->payementModel->addDeliveryAddress($_POST, $_SESSION['customer_id'] ?? -1, session_id());
        }
        elseif(isset($_POST['oldAdress'])) {
            $this->payementModel->useCustomerAdress($_SESSION['customer_id'], session_id());
        }

        // Choix du mode de paiement
        if(isset($_POST['payement'])) {
            $this->payementModel->addPayementMethod($_POST['payement'], $_SESSION['customer_id'] ?? -1, session_id());
            switch ($_POST['payement']) {
                case 'paypal':
                    echo '<script type="text/javascript">window.open("https://www.paypal.com", "_blank");</script>';
                    $template = $this->twig->load('thankyou.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                case 'cb':
                    $template = $this->twig->load('cb.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                case 'cheque':
                    $template = $this->twig->load('cheque.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                case 'virement':
                    $template = $this->twig->load('virement.twig');
                    echo $template->render(array('customer' => $customer));
                    exit();
                default:
                    header('Location: ./');
                    break;
            }
        }

        // Choix du mode de paiement
        $template = $this->twig->load('payement.twig');
        echo $template->render(array('customer' => $customer));
    }

    /**
     * Displays the thank you page after a successful payment.
     */
    public function thankYou() {
        // Page de remerciement avec les détails de la commande
        $template = $this->twig->load('thankyou.twig');
        echo $template->render(array());
    }
}
