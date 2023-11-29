<?php

class Login_controller {
    private $user_model;

    public function __construct() {
        $this->user_model = new user_model();
    }

    function logIn () {
        if (isset($_POST['login']) && isset($_POST['password'])) {
            foreach ($this->user_model->getUtilisateur($_POST['login'], $_POST['password']) as $user) {
                $login = $user['username'];
                $password = $user['password'];
                $cdi = $user['customer_id'];           
            }
            if (isset ($login) && isset ($password)) {
                $_SESSION['customer_id'] = $cdi;

                foreach ($this->user_model->getCustomer($cdi) as $customer) {
                    $_SESSION['forname'] = $customer['forname'];
                    $_SESSION['surname'] = $customer['surname'];
                }
                header('Location: ./');
            }
            var_dump($user_model->getAdmin($_POST['login'], $_POST['password']));
            foreach ($this->user_model->getAdmin($_POST['login'], $_POST['password']) as $admin) {
                $login = $admin['username'];
                $password = $admin['password'];
                $id = $admin['id'];
            }

            if (isset ($login) && isset ($password)) {
                $_SESSION['admin_id'] = $id;
                header('Location: ./');
            }
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('login.twig');
        echo $template->render(array());    
    }

    function logOut () {
        session_destroy();
        header('Location: ./');
    }

    function register(){
        if (!(empty($_POST))) {
            $user_model = new user_model();
            $user_model->addCustomer($_POST['forname'], $_POST['surname'], $_POST['phone'], $_POST['email'], 1);

            foreach ($user_model->getCustomerByPhone($_POST['phone']) as $id) {
                $cid = $id['id'];
            };
            $user_model->addAdress($cid, $_POST['add1'], $_POST['add2'], $_POST['city'], $_POST['postcode']);
            $user_model->addLogin($cid, $_POST['username'], $_POST['password']);
            header('Location: ./');
        }
        $loader = new Twig\Loader\FilesystemLoader('view');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('register.twig');
        echo $template->render(array());
    }
}

?>