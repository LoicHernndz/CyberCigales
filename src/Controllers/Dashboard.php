<?php
namespace Controllers;

use Views\Homepage\HomepageView;
use Attributes\Route;

/**
 * Contrôleur du tableau de bord utilisateur
 *
 * Affiche la page d'accueil personnalisée pour les utilisateurs connectés.
 */
#[Route('/dashboard', name: 'dashboard')]
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
            redirect(url('user_login'));
        }

        $view = new HomepageView();
        $view->render();
    }
}
