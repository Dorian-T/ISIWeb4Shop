<?php

require_once 'model/connect.php';

/**
 * Class LivraisonModel
 * This class is the model used for the livraison page.
 */
class LivraisonModel {

    /**
     * The PDO instance used to connect to the database.
     */
    private static $connexion;

    /**
     * LivraisonModel constructor.
     */
    public function __construct()
    {
        $dsn="mysql:dbname=".BASE.";host=".SERVER;
        try{
            self::$connexion=new PDO($dsn,USER,PASSWD);
        }
        catch(PDOException $e){
            printf("Échec de la connexion : %s\n", $e->getMessage());
            $this->connexion = null;
        }
    }

    /**
     * This function returns the customers due to the id in the database.
     * @param $customer_id int The id of the customer.
     * @return array
     */
    public function getAddressByCustomer($customer_id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $data=self::$connexion->prepare($sql);
        $data->execute([$customer_id]);
        return $data->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * This function inserts the delivery address in the database.
     * @param $address array The delivery address
     * @param $customerId int The id of the customer
     * @param $sessionId int The id of the session.
     */
    public function addDeliveryAddress($address, $customerId=null, $sessionId=null) {
        $sql = "INSERT INTO delivery_addresses (id, firstname, lastname, add1, add2, city, postcode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data=self::$connexion->prepare($sql);
		$data->execute([$customerId, $address['firstname'], $address['lastname'], $address['add1'], $address['add2'], $address['city'], $address['postcode'], $address['phone'], $address['email']]);
    }
}
?>