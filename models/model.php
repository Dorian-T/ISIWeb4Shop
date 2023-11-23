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

?>