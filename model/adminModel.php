<?php

require_once 'model/connect.php';

/**
 * Class AdminModel
 * This class is the model used for the admin page.
 */
class AdminModel {

	/**
	 * The PDO instance used to connect to the database.
	 */
	private static $connexion;

	/**
	 * AdminModel constructor.
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
	 * Checks if a user is an admin.
	 *
	 * @param int $id The ID of the user.
	 * @return bool Returns true if the user is an admin, false otherwise.
	 */
	public function isAdmin($id): bool {
		$sql = "SELECT id FROM admin WHERE id = ?";
		$data = self::$connexion->prepare($sql);
		$data->execute([$id]);
		if ($data->fetch(PDO::FETCH_ASSOC) === false) {
			return false;
		}
		return true;
	}

	/**
	 * This function returns the orders in the database.
	 * @return array
	 */
	public function getAllOrders() {
		$sql = "SELECT DISTINCT * FROM orders";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the products in the database.
	 * @return array
	 */
	public function getAllproducts() {
		$sql = "SELECT * FROM products";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the products due to the id in the database.
	 * @param $id int The id of the product.
	 * @return array
	 */
	public function getProduct($id) {
		$sql = "SELECT * FROM products WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($id));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function updates the orders with the status due to the id in the database.
	 * @param $id int The id of the order
	 * @param $status int The status of the order.
	 */
	public function updateOrderAdmin($id, $status) {
		$sql = "UPDATE orders SET status = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($status, $id));
	}
	
	/**
	 * This function updates the products due to the id in the database.
	 * @param $id int The id of the product
	 * @param $name string The name of the product
	 * @param $description string The description of the product
	 * @param $price int The price of the product
	 * @param $quantity int The quantity of the product
	 */
	public function updateProduct($id, $name, $description, $price, $quantity) {
		$sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($name, $description, $price, $quantity, $id));
	}

	/**
	 * This function deletes the products due to the id in the database.
	 * @param $id int The id of the product.
	 */
	public function deleteProduct($id) {
		$sql = "DELETE FROM products WHERE id = ?";
		$data = self::$connexion->prepare($sql);
		$data->execute(array($id));
	}

	/**
	 * Validates an order.
	 *
	 * @param int $orderId The ID of the order to validate.
	 * @return void
	 */
	public function validateOrder($orderId)
	{
		$sql = "UPDATE orders SET status = 10 WHERE id = ?";
		$data = self::$connexion->prepare($sql);
		if (!$data->execute([$orderId])) {
			print_r($data->errorInfo());
		}
	}

	public function getOrdersById($id)
	{
		$sql = "SELECT * FROM orders WHERE id = ? AND status != '0' LIMIT 1";
		$data = self::$connexion->prepare($sql);
		$data->execute([$id]);
		$result = $data->fetch(PDO::FETCH_ASSOC);
		if ($result === false) {
			return null;
		}
		return $result;
	}

	
}
