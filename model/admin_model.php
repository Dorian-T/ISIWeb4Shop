<?php
require_once 'model/connect.php';

class admin_model {

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

	function getAllOrders() {
		$sql = "SELECT * FROM orders o JOIN orderitems oi ON o.id = oi.order_id ";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	function getOrder() {
		$sql = "SELECT * FROM orders";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	function getAllproducts() {
		$sql = "SELECT * FROM products";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	function getProduct($id) {
		$sql = "SELECT * FROM products WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($id));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	public function updateOrderAdmin($id, $status) {
		$sql = "UPDATE orders SET status = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($status, $id));
	}	
	
	public function updateProduct($id, $name, $description, $price, $quantity) {
		$sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($name, $description, $price, $quantity, $id));
	}

	public function deleteProduct($id) {
		$sql = "DELETE FROM products WHERE id = ?";
		$data = self::$connexion->prepare($sql);
		$data->execute(array($id));
	}
	
}

?>