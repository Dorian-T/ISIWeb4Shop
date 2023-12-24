<?php

require_once 'model/connect.php';

/**
 * Class FactureModel
 * This class is the model used for the facture page.
 */
class FactureModel {

	/**
	 * The PDO instance used to connect to the database.
	 */
	private static $connexion;

	/**
	 * FactureModel constructor.
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
	 * This function returns the devilery addresses in the database.
	 * @return array
	 */
	public function getDeliveryAddress()
	{
		$sql = "SELECT * FROM delivery_addresses ";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the name, price and quantity of the products in the database.
	 * @param $order_id int The id of the order.
	 * @return array
	 */
	public function getPDF($order_id) {
		$sql = "SELECT name,price,quantity FROM products p join orderitems o on p.id =o.product_id where order_id=?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($order_id));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

}

?>