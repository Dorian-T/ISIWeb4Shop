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

class Login_Model {

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

	function createUser($username, $password) {
		try {
			// Utilisez password_hash pour stocker les mots de passe de manière sécurisée
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			$stmt = $this->pdo->prepare("INSERT INTO logins (customer_id, username, password) VALUES (NULL, ?, ?)");
			$stmt->execute([$username, $hashedPassword]);

			return true;
		} catch (PDOException $e) {
			echo "Erreur lors de la création du compte : " . $e->getMessage();
			return false;
		}
	}

	function getUserByUsername($username) {
		try {
			$stmt = $this->pdo->prepare("SELECT * FROM logins WHERE username = ?");
			$stmt->execute([$username]);

			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
			return false;
		}
	}
}

?>