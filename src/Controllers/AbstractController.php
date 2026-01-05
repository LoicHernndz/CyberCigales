<?php
namespace Controllers;

/**
 * Interface contenant les methodes necessaires a un controller fonctionnel, selon la mise en place courante du routeur (index.php)
 */
abstract class AbstractController {

    /**
     * control() est la methode par defaut que designe le routeur, c'est l'action qui sera effectué immediatement a l'execution
     */
    function control(){
        if ($_SERVER['REQUEST_METHOD'] === "GET"){
            $this->getMethod();
        } else if ($_SERVER['REQUEST_METHOD']){
            $this->postMethod();
        }
    }

    function connexionVerify() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('/user/login');
            return;
        }
    }

    abstract function getMethod();
    function postMethod(){
        echo 'ERREUR 404';
    }
}