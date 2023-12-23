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
		$data->execute([$id_product, $name_user, $stars, $title, $description]);
	}

	public function removeProduct($id, $quantity) {
        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $data = self::$connexion->prepare($sql);
        $data->execute([$quantity, $id]);
    }
}
?>
