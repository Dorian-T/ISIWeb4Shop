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

	/**
	 * Retrieves the cart ID associated with a customer ID.
	 *
	 * @param int $id The customer ID.
	 * @return int The cart ID.
	 */
	public function getCartIdByCustomerId(int $id): int {
		$sql = 'SELECT id FROM orders WHERE customer_id = ? AND status = 0';
		$data = self::$connexion->prepare($sql);
		$data->execute([$id]);
		$data = $data->fetch(PDO::FETCH_ASSOC);
		return $data ? $data['id'] : -1;
	}

	/**
	 * Retrieves the cart ID associated with a session ID.
	 *
	 * @param string $sessionId The session ID.
	 * @return int The cart ID.
	 */
	public function getCartIdBySessionId(string $sessionId): int {
		$sql = 'SELECT id FROM orders WHERE session = ? AND status = 0';
		$data = self::$connexion->prepare($sql);
		$data->execute([$sessionId]);
		$data = $data->fetch(PDO::FETCH_ASSOC);
		return $data ? $data['id'] : -1;
	}

	/**
	 * Creates a cart for a given session ID and customer ID.
	 *
	 * @param string $sessionId The session ID.
	 * @param int|null $customerId The customer ID (optional).
	 * @return void
	 */
	public function createCart(string $sessionId, int $customerId = null): void {
		$sql = 'INSERT INTO orders (customer_id, registered, date, status, session, total) VALUES (?, ?, ?, ?, ?, ?)';
		$data = self::$connexion->prepare($sql);
		$data->execute([$customerId, 0, date('Y-m-d'), 0, $sessionId, 0]);
	}

	/**
	 * Adds a product to the cart.
	 *
	 * @param int $cartId The ID of the cart.
	 * @param array $product The product details.
	 * @param int $quantity The quantity of the product to add.
	 * @return void
	 */
	public function addProductToCart(int $cartId, array $product, int $quantity): void {
		// Ajout du produit au panier
		if($this->isProductInCart($cartId, $product['id'])) {
			$sql = 'UPDATE orderitems SET quantity = quantity + ? WHERE order_id = ? AND product_id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$quantity, $cartId, $product['id']]);
		} else {
			$sql = 'INSERT INTO orderitems (order_id, product_id, quantity) VALUES (?, ?, ?)';
			$data = self::$connexion->prepare($sql);
			$data->execute([$cartId, $product['id'], $quantity]);
		}

		// Mise à jour du total du panier
		$sql = 'UPDATE orders SET total = total + ? WHERE id = ?';
		$data = self::$connexion->prepare($sql);
		$data->execute([$product['price'], $cartId]);
	}

	/**
	 * Checks if a product is in the cart.
	 *
	 * @param int $cartId The ID of the cart.
	 * @param int $productId The ID of the product.
	 * @return bool Returns true if the product is in the cart, false otherwise.
	 */
	private function isProductInCart(int $cartId, int $productId): bool {
		$sql = 'SELECT * FROM orderitems WHERE order_id = ? AND product_id = ?';
		$data = self::$connexion->prepare($sql);
		$data->execute([$cartId, $productId]);
		return $data->fetch(PDO::FETCH_ASSOC) !== false;
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
