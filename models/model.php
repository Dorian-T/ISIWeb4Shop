<?php 

function open_connection_DB() {
	global $connexion;

	$connexion = mysqli_connect(SERVEUR, UTILISATEUR, MOTDEPASSE, BDD);
	if (mysqli_connect_errno()) {
	    printf("Échec de la connexion : %s\n", mysqli_connect_error());
	    exit();
	}
}

/**
 *  	Ferme la connexion courante
 * */
function close_connection_DB() {
	global $connexion;

	mysqli_close($connexion);
}

function update_db($requete) {
	global $connexion;
	$res = mysqli_query($connexion, $requete);
	return $res;
}

function req_products() {
	global $connexion;
	$requete = "SELECT * FROM products";
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);
	return $instances;
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


?>