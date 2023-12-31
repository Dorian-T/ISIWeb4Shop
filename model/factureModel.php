<?php

class FactureModel {

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
     * Get order details by order ID from the database.
     *
     * @param int $id
     * @return array|null
     */
    public function getOrderById($id) {
        $sql = "SELECT * FROM orders WHERE id = " . $id . " AND status != '0' LIMIT 1";
        $data=self::$connexion->prepare($sql);
        $data->execute();
        return $data->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get order items by order ID from the database.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderItems($orderId) {
        $sql = "  SELECT orderitems.quantity AS orderQuantity, P.* FROM orderitems JOIN products P ON P.id = orderitems.product_id WHERE order_id = " . $orderId;
        $data=self::$connexion->prepare($sql);
        $data->execute();
        return $data->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get address details by address ID from the database.
     *
     * @param int $id
     * @return array|null
     */
    public function getAddressById($id) {
        $sql= "SELECT * FROM delivery_addresses WHERE id = " . $id . " LIMIT 1";
        $data=self::$connexion->prepare($sql);
        $data->execute();
        return $data->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get product details by product ID from the database.
     *
     * @param int $id
     * @return array|null
     */
    public function getProductById($id) {
        $sql= " SELECT P.*, C.name AS category, ROUND(AVG(R.stars), 1) AS rate FROM products P JOIN categories C JOIN reviews R ON P.id = R.id_product WHERE P.id = " . $id . " LIMIT 1";
        $data=self::$connexion->prepare($sql);
        $data->execute();
        return $data->fetch(PDO::FETCH_ASSOC);
    }
}
