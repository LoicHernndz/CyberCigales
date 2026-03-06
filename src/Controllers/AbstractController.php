<?php
namespace Controllers;

/**
 * Interface contenant les methodes necessaires a un controller fonctionnel, selon la mise en place courante du routeur (index.php)
 *
 * OWASP A01 - Broken Access Control : vérification d'authentification et protection CSRF
 */
abstract class AbstractController {

    /**
     * control() est la methode par defaut que designe le routeur, c'est l'action qui sera effectué immediatement a l'execution
     */
    function control(){
        if ($_SERVER['REQUEST_METHOD'] === "GET"){
            $this->getMethod();
        // OWASP A05 - Security Misconfiguration : on vérifie explicitement que c'est un POST
        // Avant, la condition était toujours vraie car $_SERVER['REQUEST_METHOD'] est toujours défini
        } else if ($_SERVER['REQUEST_METHOD'] === "POST"){
            $this->postMethod();
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté
     *
     * OWASP A07 - Identification and Authentication Failures :
     * chaque page protégée doit vérifier l'authentification
     */
    function connexionVerify() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/user/login');
            return;
        }
    }

    /**
     * Vérifie le token CSRF sur les requêtes POST
     *
     * OWASP A01 - Broken Access Control :
     * Le CSRF (Cross-Site Request Forgery) permet à un attaquant de forcer un utilisateur
     * connecté à exécuter des actions à son insu (ex: un lien piégé qui supprime son compte).
     * Cette méthode vérifie que le formulaire soumis contient bien un token CSRF valide,
     * prouvant que la requête vient bien de notre site et non d'un site malveillant.
     *
     * Appeler cette méthode en première ligne de postMethod() dans chaque contrôleur
     * qui traite un formulaire.
     */
    protected function csrfVerify(): void
    {
        if (!csrf_verify()) {
            // 403 Forbidden : la requête est rejetée car le token est absent ou invalide
            http_response_code(403);
            die('Erreur de sécurité : jeton CSRF invalide. Veuillez rafraîchir la page et réessayer.');
        }
    }

    abstract function getMethod();
    function postMethod(){
        echo 'ERREUR 404';
    }
}
