<?php
require_once "connect.php";

class Produits_modele {
	/** Objet contenant la connexion pdo à la BD */
	private static $connexion;

	/** Constructeur établissant la connexion */
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

	public function getProductById($id) {
		$sql = "SELECT * FROM products WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	public function getReviewsByProductId($id) {
		$sql = "SELECT * FROM reviews WHERE id_product = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id]);
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}


	// Panier :

	/**
	 * Ajoute un produit au panier de l'utilisateur.
	 *
	 * @param int $sessionId L'ID de la session de l'utilisateur.
	 * @param array $product Le produit à ajouter.
	 * @param int $quantity La quantité à ajouter.
	 *
	 * @return void
	 */
	public function addProductToCart(array $product, int $quantity): void {
		// Récupération ou création du customer
		$this->getCustomer(session_id());

		// Création ou mise à jour du panier
		$this->updateCart($product, $quantity);

		// Ajout du produit au panier
		$this->addProduct($product, $quantity);
	}

	/**
	 * Met le customer_id dans la session si le customer existe, ou crée un nouveau customer.
	 * Met hasCart à true, dans la session, si le customer a un panier, false sinon.
	 *
	 * @param string $sessionId The session ID.
	 *
	 * @return void
	 */
	private function getCustomer(string $sessionId): void {
		// Si on connait le customer_id, on vérifie s'il a un panier
		if(!isset($_SESSION['customer_id'])) {
			$sql = 'SELECT id FROM orders WHERE customer_id = ?';
			$data=self::$connexion->prepare($sql);
			$data->execute([$sessionId]);
			$data = $data->fetch(PDO::FETCH_ASSOC);
			if($data) {
				$_SESSION['hasCart'] = true;
			}
			else {
				$_SESSION['hasCart'] = false;
			}
		}
		else {
			// Récupération du customer
			$sql = 'SELECT customer_id
					FROM orders
					WHERE session = ?';
			$data=self::$connexion->prepare($sql);
			$data->execute([$sessionId]);
			$data = $data->fetch(PDO::FETCH_ASSOC);

			// Si le customer n'existe pas, on le crée
			if(!$data) {
				$sql = 'INSERT INTO customers (registered) VALUES (0)';
				$data=self::$connexion->prepare($sql);
				$data->execute([$sessionId]);
				$data = $data->fetch(PDO::FETCH_ASSOC);
				$_SESSION['customer_id'] = self::$connexion->lastInsertId();
				$_SESSION['hasCart'] = false;
			}
			else {
				$_SESSION['customer_id'] = $data['customer_id'];
				$_SESSION['hasCart'] = true;
			}
		}
	}

	/**
	 * Met à jour le panier de l'utilisateur.
	 * Si l'utilisateur n'a pas de panier, en crée un.
	 * Met le numéro du panier dans la session.
	 *
	 * @param array $product Le produit à ajouter.
	 * @param int $quantity La quantité à ajouter.
	 *
	 * @return void
	 */
	private function updateCart(array $product, int $quantity): void {
		// Si l'utilisateur a déjà un panier, on le met à jour
		if($_SESSION['hasCart']) {
			// Récupération du panier
			$sql = 'SELECT id
					FROM orders
					WHERE customer_id = ? AND status = 0';
			$data = self::$connexion->prepare($sql);
			$data->execute([$_SESSION['customer_id']]);
			$id = $data->fetch(PDO::FETCH_ASSOC)['id'];
			$_SESSION['cartId'] = $id;

			// Mise à jour du panier
			$sql = 'UPDATE orders
					SET date = ? AND session = ? AND total = total + ?
					WHERE id = ?';
			$data=self::$connexion->prepare($sql);
			$data->execute([date('Y-m-d'), session_id(), $product['price'] * $quantity, $id]);
		}

		// Si l'utilisateur n'a pas de panier, on en crée un
		else {
			// Est-ce que le customer est connecté ?
			$sql = 'SELECT registered FROM customers WHERE id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$_SESSION['customer_id']]);
			$registered = $data->fetch(PDO::FETCH_ASSOC)['registered'];

			// Création du panier
			$sql = 'INSERT INTO orders (customer_id, registered, date, status, session, total)
					VALUES (?, ?, ?, ?, ?, ?)';
			$data=self::$connexion->prepare($sql);
			$data->execute(
				[$_SESSION['customer_id'], $registered, date('Y-m-d'), 0, session_id(), $product['price'] * $quantity]
			);
			$_SESSION['cartId'] = self::$connexion->lastInsertId();
		}
	}

	/**
	 * Ajoute un produit au panier de l'utilisateur.
	 * Si le produit est déjà dans le panier, incrémente la quantité.
	 *
	 * @param array $product Le produit à ajouter.
	 * @param int $quantity La quantité à ajouter.
	 */
	private function addProduct(array $product, int $quantity): void {
		// TODO : si le produit est déjà dans le panier, on incrémente la quantité
		// Ajout du produit au panier
		$sql = 'INSERT INTO orderitems (order_id, product_id, quantity) VALUES (?, ?, ?)';
		$data=self::$connexion->prepare($sql);
		$data->execute([$_SESSION['cartId'], $product['id'], $quantity]);
	}

	public function addComment($id_product, $name_user, $stars, $title, $description) {
		$sql = "INSERT INTO reviews (id_product, name_user, stars, title, description) VALUES (?, ?, ?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id_product, $id_customer, $comment, $note]);
	}

	private function removeProduct($id, $quantity) {
		$sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$quantity, $id]);
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

class admin_model {

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

	function getAllOrders() {
		$sql = "SELECT * FROM orders o JOIN orderitems oi ON o.id = oi.order_id ";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	function getOrder() {
		$sql = "SELECT * FROM orders";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	function getAllproducts() {
		$sql = "SELECT * FROM products";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}

	public function updateOderAdmin($id, $status) {
		$sql = "UPDATE orders SET status = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($status, $id));
	}
	
	public function updateProduct($id, $name, $description, $price, $quantity, $cat_id) {
		$sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, cat_id = ? WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($name, $description, $price, $quantity, $cat_id, $id));
	}
}

class payement_model {

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

	function updateOrder($id, $cid, $r, $adress, $payement, $status, $total,$session) {
		$sql = "UPDATE orders SET customer_id = ?,registered = ?,delivery_add_id = ?,payment_type = ?, status = ?, total = ?, session = ?  where id= ?";
		$data=self::$connexion->prepare($sql);
		$data->execute(array($id, $cid, $r, $adress, $payement, $status, $total, $session));
	}
}

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
