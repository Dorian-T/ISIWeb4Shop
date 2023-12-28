<?php

/**
 * Class LivraisonController
 *
 * This class is responsible for handling the delivery page functionality.
 */
class LivraisonController {

    /**
     * The delivery model instance.
     */
    private $livraisonModel;

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;

    /**
     * LivraisonController constructor.
     *
     * @param $twig The Twig instance used for rendering templates.
     */
    public function __construct($twig){
        $this->livraisonModel = new LivraisonModel();
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    /**
     * Gets the delivery address of the logged-in user.
     * If the user is not logged in, returns false.
     *
     * @return array|false Delivery address details or false if the user is not logged in.
     */
    public function getDeliveryAddressForLoggedInUser() {
        $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;
        if (isset($_SESSION['customer_id'])) {
            $customerId = $_SESSION['customer_id'];
            return $this->livraison_model->getAddressByCustomer($customerId);
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = session_id();
            $address = $_POST;
            $this->livraison_model->addDeliveryAddress($address, null, $sessionId);
        }
    
        $template = $this->twig->load('delivery.twig');
        echo $template->render(array('customer' => $customer));
    }


    /**
     * Displays the delivery address of the logged-in user, or allows the user to add a new address.
     * 
     * @return array
     */
    public function showDeliveryAddressOrForm(): void {
        $customer = (isset($_SESSION['customer_id'])) ? $this->userModel->getCustomer(intval($_SESSION['customer_id'])) : null;
        if (isset($_SESSION['customer_id'])) {
            // Récupérez l'adresse de livraison existante depuis la base de données
            $livraisonModel = new LivraisonModel();
            $customerId = $_SESSION['customer_id']; // Assurez-vous que vous avez une variable de session pour stocker l'ID du client
            $deliveryAddress = $livraisonModel->getAddressByCustomer($customerId);
    
            $template = $this->twig->load('delivery.twig');
            echo $template->render(array('delivery_address' => $deliveryAddress));
        } else {
            // Affichez le formulaire pour ajouter une nouvelle adresse de livraison
            $template = $this->twig->load('delivery.twig');
            echo $template->render(array('delivery_address' => null, 'customer' => $customer));
        }
    }
}
