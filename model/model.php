<?php
require("connect.php");

class Produits_modele {
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

    function req_products() {
        $sql = "SELECT * FROM products";
        $data=self::$connexion->query($sql);
        return $data->fetchAll(PDO::FETCH_ASSOC);
    }


	function getCategory() {
		$sql = "SELECT * FROM categories";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data;
	}

	function getProductsByCategory($category) {
		$sql = "SELECT * FROM products WHERE cat_id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$category]);
		return $data;
	}
}

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

	function getUtilisateur($login, $password) {
		$sql = "SELECT * FROM users WHERE login = ? AND password = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$login, $password]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getCustomer ($i)
	{
		$sql = "SELECT * FROM customers WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$i]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getCustomerByPhone ($phone)
	{
		$sql = "SELECT * FROM customers WHERE phone = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$phone]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function addCustomer ($forname, $surname, $phone, $email, $registered)
	{
		$sql = "INSERT INTO customers (forname, surname, phone, email, registered) VALUES (?, ?, ?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$forname, $surname, $phone, $email, $registered]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function addLogin ($cid, $username, $password)
	{
		$sql = "INSERT INTO logins (customer_id, login, password) VALUES (?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$cid, $username, $password]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function addAdress($cid, $add1, $add2, $city, $postcode)
	{
		$sql = "INSERT INTO addresses (customer_id, add1, add2, city, postcode) VALUES (?, ?, ?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$cid, $add1, $add2, $city, $postcode]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	function getAdmin($login, $password) {
		$sql = "SELECT * FROM admin where (username=? and password=?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$login, $password]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}


}

?>