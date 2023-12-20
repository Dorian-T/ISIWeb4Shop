<?php

class LivraisonController {

    private $livraison_model;
    private $twig;
    private $userModel;

    function __construct($twig){
        $this->livraison_model = new Livraison_model();
        $this->twig = $twig;
    }

    /**
     * Obtient l'adresse de livraison de l'utilisateur connecté.
     * Si l'utilisateur n'est pas connecté, retourne false.
     *
     * @return array|false Les détails de l'adresse de livraison ou false si l'utilisateur n'est pas connecté.
     */
    public function getDeliveryAddressForLoggedInUser() {
        if (isset($_SESSION['customer_id'])) {
            $customerId = $_SESSION['customer_id'];
            return $this->livraison_model->getAddressByCustomer($customerId);
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = session_id();
            $address = $_POST;
            $this->livraison_model->addDeliveryAddress($address, null, $sessionId);
        }
    
        $template = $this->twig->load('livraison.twig');
        echo $template->render(array());
    }


    /**
     * Affiche l'adresse de livraison de l'utilisateur connecté ou permet à l'utilisateur d'ajouter une nouvelle adresse.
     */
    public function showDeliveryAddressOrForm() {
        $delivery_address = $this->getDeliveryAddressForLoggedInUser();

        $template = $this->twig->load('livraison.twig');
        echo $template->render(array('delivery_address' => $delivery_address));
    }
}
