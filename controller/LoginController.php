<?php

/**
 * Class LoginController
 *
 * This class handles the login functionality of the application.
 */
class LoginController {
    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;

    /**
     * LoginController constructor.
     *
     * @param $twig The Twig instance used for rendering views.
     */
    public function __construct($twig) {
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    /**
     * Logs in the user.
     *
     * @return void
     */
    public function logIn () {
        if (isset($_POST['login']) && isset($_POST['mdp'])) {

            $user = $this->userModel->getUtilisateurByLogin($_POST['login']);

            if ($user != null) {
                $pwdHashed = $user['password'];
                $cdi = $user['customer_id'];
                
                if (password_verify($_POST['mdp'], $pwdHashed)) {

                    $_SESSION['customer_id'] = $cdi;
                    $_SESSION['firstname'] = $user['forname'];
                    $_SESSION['lastname'] = $user['surname'];
                    header('Location: index.php?action=home');
                    exit();
                }
                else {
                    echo '<script>alert("La connexion a échoué")</script>';
                }
            }

            $userAdmin = $this->userModel->getAdminByLogin($_POST['login']);

            if ($userAdmin != null) {
                $pwdHashed = $userAdmin['password'];
                $id = $userAdmin['id'];

                if (password_verify($_POST['mdp'], $pwdHashed)) {
                    $_SESSION['admin_id'] = $id;
                    $_SESSION['username'] = $userAdmin['username'];
                    header('Location: index.php?action=home');
                    exit();
                }
                else {
                    echo '<script>alert("La connexion a échoué")</script>';
                }
            }
        }

        $template = $this->twig->load('login.twig');
        echo $template->render(array());
    }
    /**
     * Logs out the user.
     */
    public function logOut() {
        // Vider les variables de session liées à l'utilisateur
        unset($_SESSION['customer_id']);
        unset($_SESSION['hasCart']);

        // Détruire la session
        session_destroy();

        // Rediriger l'utilisateur vers la page d'accueil
        header('Location: index.php?action=home');
        exit();
    }

    /**
     * Registers a new user.
     */
    public function register(){
        if (!(empty($_POST))) {
            $userModel = new UserModel();

            $cid = $userModel->addCustomer(
                $_POST['forname'],
                $_POST['surname'],
                $_POST['phone'],
                $_POST['email'],
                1,
                $_POST['add1'],
                $_POST['add2'],
                $_POST['city'],
                $_POST['postcode']
            );
            $userModel->addLogin($cid, $_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT));
            header('Location: index.php?action=registered');
            exit();
        } else {
            $template = $this->twig->load('register.twig');
            echo $template->render(array());
        }
    }

    /**
     * Registers an admin.
     */
    public function registerAdmin(){
        if (!(empty($_POST))) {
            $userModel = new UserModel();

            $userModel->addAdmin($_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT));
            var_dump($userModel);
            header('Location: index.php?action=registered');
            exit();
        }
        else {
            $template = $this->twig->load('registerAdmin.twig');
            echo $template->render(array());
        }
    }
}
