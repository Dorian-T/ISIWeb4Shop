<?php

/**
 * Class Model
 * This class is the model used to connect to the database.
 */
class Model {

	/**
	 * The PDO instance used to connect to the database.
	 */
	protected static $connexion;

	/**
	 * ProductModel constructor.
	 */
	public function __construct() {
		$dsn="mysql:dbname=".BASE.";host=".SERVER;
		try {
			self::$connexion=new PDO($dsn,USER,PASSWD);
		}
		catch(PDOException $e) {
			printf("Échec de la connexion : %s\n", $e->getMessage());
			$this->connexion = null;
		}
	}
}
