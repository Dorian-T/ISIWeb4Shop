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
	 * This function returns all the orders in the database.
	 * @return array
	 */
	public function getAllOrders() {
		$sql = "SELECT * FROM orders o JOIN orderitems oi ON o.id = oi.order_id ";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the orders in the database.
	 * @return array
	 */
	public function getOrder() {
		$sql = "SELECT * FROM orders";
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
	
}
?>
