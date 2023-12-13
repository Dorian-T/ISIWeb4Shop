<?php

class Login_controller {
    private $user_model;

    public function __construct() {
        $this->user_model = new user_model();
    }

    function logIn () {
        if (isset($_POST['login']) && isset($_POST['mdp'])) {

            $user = $this->user_model->getUtilisateurByLogin($_POST['login']);

            if ($user != null) {
                $login = $user['username'];
                $pwdHashed = $user['password'];
                $cdi = $user['customer_id'];  
                
                if (password_verify($_POST['mdp'], $pwdHashed)) {

                    $_SESSION['customer_id'] = $cdi;
                    $_SESSION['firstname'] = $user['forname'];
                    $_SESSION['lastname'] = $user['surname'];
                    header('Location: index.php?action=home');
                    exit();
                }
            }

            $userAdmin = $this->user_model->getAdminByLogin($_POST['login']);

            if ($userAdmin != null) {
                $login = $userAdmin['username'];
                $pwdHashed = $userAdmin['password'];
                $id = $userAdmin['id'];

                if (password_verify($_POST['mdp'], $pwdHashed)) {
                    $_SESSION['admin_id'] = $id;
                    $_SESSION['username'] = $userAdmin['username'];
                    header('Location: index.php?action=home');
                    exit();
                }
            }
        }

        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('login.twig');
        echo $template->render(array());   
    }

    function logOut () {
        session_destroy();
        header('Location: index.php?action=home');
    }

    function register(){
        if (!(empty($_POST))) {
            $user_model = new user_model();
            
            $cid = $user_model->addCustomer($_POST['forname'], $_POST['surname'], $_POST['phone'], $_POST['email'], 1, $_POST['add1'], $_POST['add2'], $_POST['city'], $_POST['postcode']);
            $user_model->addLogin($cid, $_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT));
            header('Location: index.php?action=registered');
            exit();
        } else {
            $loader = new Twig\Loader\FilesystemLoader('view');
            $twig = new Twig\Environment($loader);
            
            $template = $twig->load('register.twig');
            echo $template->render(array());
        }
    }

    function registerAdmin(){
        if (!(empty($_POST))) {
            $user_model = new user_model();
            
            $user_model->addAdmin($_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT));
            var_dump($user_model);
            header('Location: index.php?action=registered');
            exit();
        } else {
            $loader = new Twig\Loader\FilesystemLoader('view');
            $twig = new Twig\Environment($loader);
            
            $template = $twig->load('registerAdmin.twig');
            echo $template->render(array());
        }
    }
}

?>