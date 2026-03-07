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
            redirect(url('user_login'));
            return;
        }
    }

    /**
     * Envoie une réponse JSON et termine l'exécution.
     */
    protected function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    abstract function getMethod();

    function postMethod(){
        http_response_code(405);
        $controller = new \Controllers\Error404\Error404();
        $controller->getMethod();
    }
}