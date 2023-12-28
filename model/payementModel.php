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
	public function updateOrder($id, $cid, $r, $adress, $payement, $status, $total,$session) {
		$sql = "UPDATE orders
				SET customer_id = ?, registered = ?, delivery_address = ?, payement = ?, status = ?, total = ?, session = ?
				WHERE id = ?";

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