<?php

require_once 'model/connect.php';
require_once 'model/model.php';

/**
 * Class LivraisonModel
 * This class is the model used for the livraison page.
 * It extends the Model class.
 */
class LivraisonModel extends Model {
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
        $sql = "INSERT INTO delivery_addresses (id, firstname, lastname, add1, add2, city, postcode, phone, email)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data=self::$connexion->prepare($sql);
		$data->execute([$customerId, $address['firstname'], $address['lastname'], $address['add1'], $address['add2'], $address['city'], $address['postcode'], $address['phone'], $address['email']]);
    }
}
