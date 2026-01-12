<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\User;

/**
 * Contrôleur de déconnexion utilisateur
 * 
 * Gère la destruction sécurisée de la session utilisateur et la redirection.
 */
class Logout extends AbstractController {

    /**
     * Déconnecte l'utilisateur
     * 
     * Supprime toutes les variables de session utilisateur et détruit la session complètement.
     * 
     * @return void
     */
    function getMethod(){
        // Je supprime la variable de session qui contient l'id de l'utilisateur
        unset($_SESSION['user_id']);
        // Je supprime la variable de session qui contient l'email de l'utilisateur
        unset($_SESSION['user_email']);
        // Je supprime la variable de session qui contient le pseudo de l'utilisateur
        unset($_SESSION['user_pseudo']);
        // Je détruis complètement la session (toutes les variables de session sont supprimées)
        session_destroy();
        // Je redirige l'utilisateur vers la page d'accueil
        redirect("/");
    }

    /**
     * Détermine si ce contrôleur doit gérer la requête
     * 
     * @param string $chemin L'URL demandée
     * @param string $method La méthode HTTP (GET, POST, etc.)
     * @return bool True si la route correspond à /user/logout en GET
     */
    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/logout" && $method === "GET";
    }
}