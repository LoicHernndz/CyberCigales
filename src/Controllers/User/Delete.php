<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Views\Homepage\HomepageView;
use Models\User\User;

/**
 * Contrôleur de suppression de compte utilisateur
 *
 * Gère la suppression du profil utilisateur : affiche la confirmation (GET)
 * et effectue la suppression avec déconnexion (POST).
 */
class Delete extends AbstractController
{
    /**
     * Affiche la page de confirmation de suppression du compte
     *
     * Redirige vers la connexion si l'utilisateur n'est pas authentifié.
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect('/user/login');
            return;
        }
        $view = new HomepageView();
        $view->render();
    }

    /**
     * Supprime le profil utilisateur et détruit la session
     *
     * @return void
     */
    public function postMethod(): void
    {
        $userModel = new User();
        $userModel->deleteProfil($_SESSION['user_id']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_pseudo']);
        session_destroy();
        redirect("/");
        $view = new HomepageView();
        $view->render();
    }
}
