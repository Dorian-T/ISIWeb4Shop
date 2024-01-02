<?php

require_once "connect.php";
require_once "model.php";

/**
 * Class UserModel
 * This class is the model used for the user page.
 * It extends the Model class.
 */
class UserModel extends Model {
	/**
	 * This function returns the user in the database.
	 * @param $login string The login of the user.
	 * @return array
	 */
	public function getUtilisateurByLogin($login) {
		$sql = "SELECT * FROM logins JOIN  customers ON customers.id = logins.customer_id  WHERE (username = ? )" ;
		$data=self::$connexion->prepare($sql);
		$data->execute(array($login));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the customers in the database.
	 * @param $i int The id of the customers.
	 * @return array
	 */
	public function getCustomer ($i)
	{
		$sql = "SELECT * FROM customers WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($i));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the admin username in the database.
	 *
	 * @param $i int The id of the admin.
	 * @return string
	 */
	public function getAdminUsername($i)
	{
		$sql = "SELECT username FROM admin WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($i));
		return $data->fetch(PDO::FETCH_ASSOC)['username'];
	}

	/**
	 * This function returns the customers by phone in the database.
	 * @param $phone string The phone of the customers.
	 * @return array
	 */
	public function getCustomerByPhone ($phone)
	{
		$sql = "SELECT * FROM customers WHERE phone = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($phone));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function updates the customers in the database.
	 * @param $forname string The forname of the customers.
	 * @param $surname string The surname of the customers.
	 * @param $phone string The phone of the customers.
	 * @param $email string The email of the customers.
	 * @param $registered string The registered of the customers.
	 * @param $add1 string The add1 of the customers.
	 * @param $add2 string The add2 of the customers.
	 * @param $city string The city of the customers.
	 * @param $postcode string The postcode of the customers.
	 * @return array
	 */
	public function addCustomer ($forname, $surname, $phone, $email, $registered, $add1, $add2, $city, $postcode)
	{
		$sql = "INSERT INTO customers (forname, surname, phone, email, registered, add1, add2, add3, postcode)
				VALUES ('".$forname."', '".$surname."', '".$phone."', '".$email."', '".$registered."', '".$add1."', '".$add2."', '".$city."', '".$postcode."')";
		$data = self::$connexion->prepare($sql);
		$data->execute();
		$sql = "SELECT id FROM customers ORDER BY id DESC LIMIT 1";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		$cid = $data->fetch(PDO::FETCH_ASSOC);
		return  $cid['id'];
	}

	/**
	 * This function adds the admin in the database.
	 * @param $username string The username of the admin.
	 * @param $password string The password of the admin.
	 */
	public function addAdmin ($username, $password)
	{
		$sql = "INSERT INTO admin (username, password) VALUES ('".$username."', '".$password."')";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($username, $password));
	}

	/**
	 * This function adds the login in the database.
	 * @param $cid int The id of the customers.
	 * @param $username string The username of the customers.
	 * @param $password string The password of the customers.
	 */
	public function addLogin ($cid, $username, $password)
	{
		$sql = "INSERT INTO logins (customer_id, username, password) VALUES (?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($cid, $username, $password));
	}

	/**
	 * This function returns the admin by his login in the database.
	 * @param $login string The login of the admin.
	 * @return array
	 */
	public function getAdminByLogin($login) {
		$sql = "SELECT * FROM admin where (username=?)";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($login));
		return $data->fetch(PDO::FETCH_ASSOC);
	}
}
