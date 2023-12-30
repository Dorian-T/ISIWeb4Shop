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
		$data->execute([$product['price']*$quantity, $cartId]);

		// Mise à jour de la quantité en stock
		$this->removeFromStock($product['id'], $quantity);
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

	public function getCart(string $sessionId, int $customerId): array {
		// Récupération du panier
		// L'utilisateur est connecté
		if($customerId !== null) {
			$cartId = $this->getCartIdByCustomerId($customerId);
		}
		// L'utilisateur n'est pas connecté
		else {
			$cartId = $this->getCartIdBySessionId($sessionId);
		}

		if($cartId) {
			$sql = 'SELECT P.id, P.name, P.price, I.quantity
					FROM orderitems I JOIN products P ON I.product_id = P.id
					WHERE order_id = ?';
			$items = self::$connexion->prepare($sql);
			$items->execute([$cartId]);
			$items = $items->fetchAll(PDO::FETCH_ASSOC);

			return $items;
		} else {
			return [];
		}
	}
	
	/**
	 * Removes a product from the database.
	 *
	 * @param int $id The ID of the product to be removed.
	 * @param float $total The total price of the product.
	 * @param string $sessionId The session ID of the user.
	 * @param int $customerId The ID of the customer.
	 * @return void
	 */
	public function removeProduct(int $id, float $total, string $sessionId, int $customerId): void {
		if($customerId !== null) {
			// Suppression du produit
			$sql = 'DELETE
					FROM orderitems
					WHERE product_id = ? AND order_id = (
						SELECT id
						FROM orders
						WHERE customer_id = ?
					)';
			$data = self::$connexion->prepare($sql);
			$data->execute([$id, $customerId]);

			// Mise à jour du total
			$sql = 'UPDATE orders SET total = total - ? WHERE customer_id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$total, $customerId]);
		}
		else {
			// Suppression du produit
			$sql = 'DELETE
					FROM orderitems
					WHERE product_id = ? AND order_id = (
						SELECT id
						FROM orders
						WHERE session = ?
					)';
			$data = self::$connexion->prepare($sql);
			$data->execute([$id, $sessionId]);

			// Mise à jour du total
			$sql = 'UPDATE orders SET total = total - ? WHERE session = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$total, $sessionId]);
		}
    }

	/**
	 * Removes unused carts.
	 *This method removes any carts that are no longer being used.
	 * A cart is considered unused if it has not been modified for 7 days,
	 * or if it has not been modified for 1 day and the customer is not logged in.
	 *
	 * @return void
	 */
	public function removeUnusedCarts(): void {
		// Récupération des id des paniers à supprimer
		$sql = 'SELECT id
				FROM orders
				WHERE status < 2
					AND (
						(date < DATE_SUB(NOW(), INTERVAL 7 DAY)
						OR (date < DATE_SUB(NOW(), INTERVAL 1 DAY) AND customer_id IS NULL))
					)';
		$data = self::$connexion->prepare($sql);
		$data->execute();
		$ids = $data->fetchAll(PDO::FETCH_ASSOC);
	
		foreach($ids as $id) {
			// Remise en stock des produits
			$this->addToStock($id['id']);

			// Suppression du panier
			$sql = 'DELETE FROM orders WHERE id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$id['id']]);
		}
	}

	/**
	 * Adds the specified cart ID to the stock.
	 *
	 * @param int $cartId The ID of the cart.
	 * @return void
	 */
	private function addToStock(int $cartId): void {
		$sql = 'SELECT product_id, quantity FROM orderitems WHERE order_id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$cartId]);
			$items = $data->fetchAll(PDO::FETCH_ASSOC);

		foreach($items as $item) {
			$sql = 'UPDATE products SET quantity = quantity + ? WHERE id = ?';
			$data = self::$connexion->prepare($sql);
			$data->execute([$item['quantity'], $item['product_id']]);
		}
	}

	/**
	 * Removes a specified quantity of a product from the stock.
	 *
	 * @param int $productId The ID of the product to remove from stock.
	 * @param int $quantity The quantity of the product to remove from stock.
	 * @return void
	 */
	private function removeFromStock(int $productId, int $quantity): void {
		$sql = 'UPDATE products SET quantity = quantity - ? WHERE id = ?';
		$data = self::$connexion->prepare($sql);
		$data->execute([$quantity, $productId]);
	}


	// Commentaires :

	/**
	 * ajout d'un commentaire
	 *
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
}
