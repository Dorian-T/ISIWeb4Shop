<?php
require_once "connect.php";

class user_model {

	/** Objet contenant la connexion pdo à la BD */
	private static $connexion;

	/** Constructeur établissant la connexion */
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

	function getUtilisateurByLogin($login) {
		$sql = "SELECT * FROM logins JOIN  customers ON customers.id = logins.customer_id  WHERE (username = ? )" ;
		$data=self::$connexion->prepare($sql);
		$data->execute(array($login));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getCustomer ($i)
	{
		$sql = "SELECT * FROM customers WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($i));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getAdmin ($i)
	{
		$sql = "SELECT * FROM admin WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($i));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getCustomerByPhone ($phone)
	{
		$sql = "SELECT * FROM customers WHERE phone = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($phone));
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function addCustomer ($forname, $surname, $phone, $email, $registered, $add1, $add2, $city, $postcode)
	{
		$sql = "INSERT INTO customers (forname, surname, phone, email, registered, add1, add2, add3, postcode) VALUES ('".$forname."', '".$surname."', '".$phone."', '".$email."', '".$registered."', '".$add1."', '".$add2."', '".$city."', '".$postcode."')";
		$data = self::$connexion->prepare($sql);
		$data->execute();
		$sql = "SELECT id FROM customers ORDER BY id DESC LIMIT 1";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		$cid = $data->fetch(PDO::FETCH_ASSOC);
		return  $cid['id'];
	}

	function addAdmin ($username, $password)
	{
		$sql = "INSERT INTO admin (username, password) VALUES ('".$username."', '".$password."')";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($username, $password));
	}

	function addLogin ($cid, $username, $password)
	{
		$sql = "INSERT INTO logins (customer_id, username, password) VALUES (?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($cid, $username, $password));
	}

	function getAdminByLogin($login) {
		$sql = "SELECT * FROM admin where (username=?)";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($login));
		return $data->fetch(PDO::FETCH_ASSOC);
	}
}

?>