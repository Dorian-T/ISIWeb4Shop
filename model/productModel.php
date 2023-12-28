<?php

require_once "connect.php";
require_once "model.php";

/**
 * Class ProductModel
 * This class is the model used for the product page.
 * It extends the Model class.
 */
class ProductModel extends Model {
	/**
	 * This function returns the products in the database.
	 * @return array
	 */
    public function getProducts() {
        $sql = "SELECT * FROM products";
        $data=self::$connexion->query($sql);
        return $data->fetchAll(PDO::FETCH_ASSOC);
    }

	/**
	 * This function returns the categories in the database.
	 * @return array
	 */
	public function getCategory() {
		$sql = "SELECT * FROM categories";
		$data=self::$connexion->prepare($sql);
		$data->execute();
		return $data;
	}

	/**
	 * This function returns the products due to the cat_id in the database.
	 * @param $category int The id of the category.
	 * @return array
	 */
	public function getProductsByCategory($category) {
		$sql = "SELECT * FROM products WHERE cat_id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$category]);
		return $data;
	}

	/**
	 * This function returns the products due to the id in the database.
	 * @param $id int The id of the product.
	 * @return array
	 */
	public function getProductById($id) {
		$sql = "SELECT * FROM products WHERE id = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id]);
		return $data->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * This function returns the reviews due to the id in the database.
	 * @param $id int The id of the product.
	 * @return array
	 */
	public function getReviewsByProductId($id) {
		$sql = "SELECT * FROM reviews WHERE id_product = ?";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id]);
		return $data->fetchAll(PDO::FETCH_ASSOC);
	}


	// Panier :

	public function getCartIdByCustomerId(int $id): int {
		$sql = "SELECT id FROM orders WHERE customer_id = ? AND status = 0";
		$data = self::$connexion->prepare($sql);
		$data->execute([$id]);
		$data = $data->fetch(PDO::FETCH_ASSOC);
		return $data ? $data['id'] : -1;
	}

	public function getCartIdBySessionId(string $sessionId): int {
		$sql = "SELECT id FROM orders WHERE session = ? AND status = 0";
		$data = self::$connexion->prepare($sql);
		$data->execute([$sessionId]);
		$data = $data->fetch(PDO::FETCH_ASSOC);
		return $data ? $data['id'] : -1;
	}

	public function createCart(string $sessionId, int $customerId = null): void {
		$sql = "INSERT INTO orders (customer_id, registered, date, status, session, total) VALUES (?, ?, ?, ?, ?, ?)";
		$data = self::$connexion->prepare($sql);
		$data->execute([$customerId, 0, date('Y-m-d'), 0, $sessionId, 0]);
	}

	public function addProductToCart(int $cartId, int $productId, int $quantity): void {
		// TODO
	}

	/**
	 * Ajoute un produit au panier de l'utilisateur.
	 *
	 * @param int $sessionId L'ID de la session de l'utilisateur.
	 * @param array $product Le produit à ajouter.
	 * @param int $quantity La quantité à ajouter.
	 *
	 * @return void
	 */
	// public function addProductToCart(array $product, int $quantity): void {
	// 	// Récupération ou création du customer
	// 	$this->getCustomer(session_id());

	// 	// Création ou mise à jour du panier
	// 	$this->updateCart($product, $quantity);

	// 	// Ajout du produit au panier
	// 	$this->addProduct($product, $quantity);
	// }

	// private function getCustomer(string $sessionId): void {
	// 	// Si le customer_id n'est pas défini, vérifie s'il existe dans la base de données
	// 	if (!isset($_SESSION['customer_id'])) {
	// 		$sql = 'SELECT customer_id FROM orders WHERE session = ?';
	// 		$data = self::$connexion->prepare($sql);
	// 		$data->execute([$sessionId]);
	// 		$data = $data->fetch(PDO::FETCH_ASSOC);
	
	// 		// Si le customer n'existe pas, créez un nouveau customer
	// 		if (!$data) {
	// 			$sql = 'INSERT INTO customers (registered) VALUES (0)';
	// 			$data = self::$connexion->prepare($sql);
	// 			$data->execute([$sessionId]);
	// 			$_SESSION['customer_id'] = self::$connexion->lastInsertId();
	// 		} else {
	// 			// Le customer existe, mettez à jour la session
	// 			$_SESSION['customer_id'] = $data['customer_id'];
	// 		}
	
	// 		// Vérifiez s'il a un panier
	// 		$_SESSION['hasCart'] = ($data !== false);
	// 	}
	// }


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

	/**
	 * ajout d'un commentaire
	 * @param $id_product int The id of the product.
	 * @param $name_user string The name of the user.
	 * @param $stars int The number of stars.
	 * @param $title string The title of the comment.
	 * @param $description string The description of the comment.
	 */
	public function addComment($id_product, $name_user, $stars, $title, $description) {
		$sql = "INSERT INTO reviews (id_product, name_user, stars, title, description) VALUES (?, ?, ?, ?, ?)";
		$data=self::$connexion->prepare($sql);
		$data->execute([$id_product, $name_user, $stars, $title, $description]);
	}

	/**
	 * This function updates the products due to the quantity in the database.
	 * @param $id int The id of the product.
	 * @param $quantity int The quantity of the product.
	 * @return array
	 */
	public function removeProduct($id, $quantity) {
        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $data = self::$connexion->prepare($sql);
        $data->execute([$quantity, $id]);
    }
}
