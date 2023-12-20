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

        $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;

        if ($customer_id) {
            return $this->livraison_model->getAddressByCustomer($customer_id);
        } else {
            return false;
        }

        $template = $this->twig->load('livraison.twig');
        echo $template->render(array('delivery_address' => $delivery_address));
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
