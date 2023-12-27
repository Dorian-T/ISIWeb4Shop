<?php
require_once 'model/connect.php';

/**
 * Class PayementModel
 * This class is the model used for the payement page.
 */
class PayementModel {

	/**
	 * The PDO instance used to connect to the database.
	 */
	private static $connexion;

	/**
	 * PayementModel constructor.
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
	 * This function updates the order in the database.
	 * @param $id int The id of the order
	 * @param $cid int The id of the customer
	 * @param $r int The registered
	 * @param $adress string The delivery address
	 * @param $payement string The payement
	 * @param $status string The status
	 * @param $total int The total
	 * @param $session int The session.
	 */
	function updateOrder($id, $cid, $r, $adress, $payement, $status, $total,$session) {
		$sql = "UPDATE orders SET customer_id = ?, registered = ?, delivery_address = ?, payement = ?, status = ?, total = ?, session = ? WHERE id = ?";

		$data=self::$connexion->prepare($sql);
		$data->execute(array($id, $cid, $r, $adress, $payement, $status, $total, $session));
	}

	/**
     * This function retrieves the details of a specific order from the database.
     * @param $orderId int The ID of the order to retrieve details for.
     * @return array|null Details of the order, or null if the order is not found.
     */
    public function getOrderDetails($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $data = self::$connexion->prepare($sql);
        $data->execute([$orderId]);

        $orderDetails = $data->fetch(PDO::FETCH_ASSOC);

        return $orderDetails ? $orderDetails : null;
    }
}
?>