<?php

class LivraisonController {

    private $livraison_model;
    private $twig;
    private $userModel;

    function __construct($twig){
        $this->livraison_model = new Livraison_model();
        $this->user_model = new User_model();
        $this->twig = $twig;
    }

    /**
     * Obtient l'adresse de livraison de l'utilisateur connecté.
     * Si l'utilisateur n'est pas connecté, retourne false.
     *
     * @return array|false Les détails de l'adresse de livraison ou false si l'utilisateur n'est pas connecté.
     */
    public function getDeliveryAddressForLoggedInUser() {
        $customer = (isset($_SESSION['customer_id'])) ? $this->user_model->getCustomer(intval($_SESSION['customer_id'])) : null;
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
        echo $template->render(array('customer' => $customer));
    }


    /**
     * Affiche l'adresse de livraison de l'utilisateur connecté ou permet à l'utilisateur d'ajouter une nouvelle adresse.
     */
    public function showDeliveryAddressOrForm(): void {
        $customer = (isset($_SESSION['customer_id'])) ? $this->user_model->getCustomer(intval($_SESSION['customer_id'])) : null;
        if (isset($_SESSION['customer_id'])) {
            // Récupérez l'adresse de livraison existante depuis la base de données
            $livraisonModel = new Livraison_model();
            $customerId = $_SESSION['customer_id']; // Assurez-vous que vous avez une variable de session pour stocker l'ID du client
            $deliveryAddress = $livraisonModel->getAddressByCustomer($customerId);
    
            $template = $this->twig->load('livraison.twig');
            echo $template->render(array('delivery_address' => $deliveryAddress));
        } else {
            // Affichez le formulaire pour ajouter une nouvelle adresse de livraison
            $template = $this->twig->load('livraison.twig');
            echo $template->render(array('delivery_address' => null, 'customer' => $customer));
        }
    }
}
