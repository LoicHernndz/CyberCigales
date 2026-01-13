<?php
namespace Controllers;

use Views\Homepage\HomepageView;

/**
 * Contrôleur du tableau de bord utilisateur
 * 
 * Affiche la page d'accueil personnalisée pour les utilisateurs connectés.
 */
class Dashboard extends AbstractController
{
    /**
     * Affiche le tableau de bord
     * 
     * Redirige vers login si l'utilisateur n'est pas connecté.
     * 
     * @return void
     */
    function getMethod(){
        // Redirige vers login si non connecte
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        $view = new HomepageView();
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/dashboard" && $method === "GET";
    }
}


