<?php

// Incluez l'autoloader de Composer s'il est utilisé
// require 'vendor/autoload.php';

// Chargez votre configuration Twig
// $loader = new \Twig\Loader\FilesystemLoader('/chemin/vers/vos/templates');
// $twig = new \Twig\Environment($loader);

// Supposons que $pdo soit votre instance PDO déjà créée
$model = new Model($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Traitement de l'inscription
        $username = $_POST['username'];
        $password = $_POST['password'];

        $success = $model->createUser($username, $password);

        if ($success) {
            echo "Compte créé avec succès!";
        } else {
            echo "Erreur lors de la création du compte.";
        }
    } elseif (isset($_POST['login'])) {
        // Traitement de la connexion
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $model->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            echo "Connexion réussie!";
        } else {
            echo "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}

// Chargez le template Twig
echo $twig->render('login_register.twig');

