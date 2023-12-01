<?php

class Login_controller {
    private $user_model;

    public function __construct() {
        $this->user_model = new user_model();
    }

    function logIn () {
        if (isset($_POST['login']) && isset($_POST['mdp'])) {
            foreach ($this->user_model->getUtilisateur($_POST['login'], $_POST['mdp']) as $i) {
                $login = $i['username'];
                $pwd = $i['password'];
                $cdi = $i['customer_id'];           
            }
            if (isset ($login) && isset ($pwd)) {
                $_SESSION['customer_id'] = $cdi;

                foreach ($this->user_model->getCustomer($cdi) as $i) {
                    $_SESSION['firstname'] = $i['forname'];
                    $_SESSION['lastname'] = $i['surname'];
                }
                header('Location: index.php?action=home');
            }

            var_dump($user_model->getAdmin($_POST['login'], $_POST['mdp']));
            foreach ($this->user_model->getAdmin($_POST['login'], $_POST['mdp']) as $i) {
                $admin = $i['username'];
                $mpd = $i['password'];
                $id = $i['id'];
            }

            if (isset ($admin) && isset ($mdp)) {
                $_SESSION['admin_id'] = $id;
                header('Location: index.php?action=home');
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
            $user_model->addCustomer($_POST['forname'], $_POST['surname'], $_POST['phone'], $_POST['mail'], 1);

            foreach ($user_model->getCustomerByPhone($_POST['phone']) as $i) {
                $cid = $i['id'];
            };
            $user_model->addAdress($cid, $_POST['add1'], $_POST['add2'], $_POST['city'], $_POST['postcode']);
            $user_model->addLogin($cid, $_POST['username'], $_POST['pwd']);
            header('Location: index.php?action=registered');
            exit();
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('register.twig');
        echo $template->render(array());
    }
}

?>