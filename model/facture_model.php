<?php
require_once 'model/connect.php';

class facture_model {

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

	public function getDeliveryAddress()
	{
		$sql = "SELECT * FROM delivery_addresses ";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	public function getPDF($order_id) {
		$sql = "SELECT name,price,quantity FROM products p join orderitems o on p.id =o.product_id where order_id=?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($order_id));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

}
?>