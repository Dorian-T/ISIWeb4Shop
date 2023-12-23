<?php
require_once 'model/connect.php';

class Livraison_model {

    private static $connexion;

    function __construct()
    {
        $dsn="mysql:dbname=".BASE.";host=".SERVER;
        try{
            self::$connexion=new PDO($dsn,USER,PASSWD);
        }
        catch(PDOException $e){
            printf("Échec de la connexion : %s\n", $e->getMessage());
            $this->connexion = NULL;
        }
    }

    /**
     * Récupère l'adresse de livraison d'un client.
     *
     * @param int $customer_id L'ID du client.
     * @return array|false Les détails de l'adresse de livraison ou false si non trouvé.
     */
    public function getAddressByCustomer($customer_id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $data=self::$connexion->prepare($sql);
        $data->execute([$customer_id]);
        return $data->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une nouvelle adresse de livraison pour un client.
     */
    public function addDeliveryAddress($address, $customerId=null, $sessionId=null) {
        $sql = "INSERT INTO delivery_addresses (id, firstname, lastname, add1, add2, city, postcode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data=self::$connexion->prepare($sql);
		$data->execute([$customerId, $address['firstname'], $address['lastname'], $address['add1'], $address['add2'], $address['city'], $address['postcode'], $address['phone'], $address['email']]);
    }
}
?>