<?php

require_once 'model/connect.php';
require_once 'model/model.php';

/**
 * Class PayementModel
 * This class is the model used for the payement page.
 * It extends the Model class.
 */
class PayementModel extends Model {
	/**
     * This function returns the customers due to the id in the database.
	 *
     * @param $customer_id int The id of the customer.
     * @return array
     */
    public function getAddressByCustomer(int $customer_id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $data=self::$connexion->prepare($sql);
        $data->execute([$customer_id]);
        return $data->fetch(PDO::FETCH_ASSOC);
    }

	/**
	 * Adds a delivery address for a customer.
	 * If the customer does not exist, it is created.
	 * The order is updated with the delivery address and the customer ID.
	 *
	 * @param array $address The delivery address to add.
	 * @param int $customerId The ID of the customer.
	 * @param string $sessionId The session ID.
	 * @return void
	 */
	public function addDeliveryAddress(array $address, int $customerId, string $sessionId): void {
		// Création de l'adresse de livraison
		$sql = "INSERT INTO delivery_addresses (firstname, lastname, add1, add2, city, postcode, phone, email)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([
			$address['firstname'],
			$address['lastname'],
			$address['add1'],
			$address['add2'],
			$address['city'],
			$address['postcode'],
			$address['phone'],
			$address['email']
		]);
		$addressId = self::$connexion->lastInsertId();

		if ($customerId == -1) {
			// Création du customer s'il n'existe pas
			$sql = "INSERT INTO customers (forname, surname, registered)
					VALUES (?, ?, 0)";
			$data=self::$connexion->prepare($sql);
			$data->execute([$address['firstname'], $address['lastname']]);
			$customerId = self::$connexion->lastInsertId();

			// Mise à jour du panier
			$sql = "SET @max_id = (SELECT MAX(id) FROM orders);
					UPDATE orders
					SET customer_id = ?, delivery_add_id = ?, status = 1
					WHERE session = ? AND id = @max_id;";
			$data=self::$connexion->prepare($sql);
			$data->execute([$customerId, $addressId, $sessionId]);
		}
		else {
			// Mise à jour du panier
			$sql = "UPDATE orders
					SET delivery_add_id = ?, registered = 1, status = 1
					WHERE customer_id = ?";
			$data=self::$connexion->prepare($sql);
			$data->execute([$addressId, $customerId]);
		}
    }

	/**
	 * Uses the customer address as a delivery address.
	 *
	 * @param int $customerId The ID of the customer.
	 * @param string $sessionId The session ID.
	 * @return void
	 */
	public function useCustomerAdress(int $customerId, string $sessionId): void {
		$sql = "SELECT forname AS firstname, surname AS lastname, add1, add2, add3 AS city, postcode, phone, email
				FROM customers
				WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$customerId]);
		$this->addDeliveryAddress($data->fetch(PDO::FETCH_ASSOC), $customerId, $sessionId);
	}

	/**
	 * Adds a payment method for a customer.
	 *
	 * @param string $method The payment method to add.
	 * @param int $customerId The ID of the customer.
	 * @param string $sessionId The session ID.
	 * @return void
	 */
	public function addPayementMethod(string $method, int $customerId, string $sessionId): void {
		$sql = "SET @max_id = (SELECT MAX(id) FROM orders);
				UPDATE orders
				SET payment_type = ?, status = 2
				WHERE status = 1 AND (customer_id = ? OR session = ?) AND id = @max_id;";
		$data=self::$connexion->prepare($sql);
		$data->execute([$method, $customerId, $sessionId]);
	}
}
