<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Views\Homepage\HomepageView;
use Models\User\User;
use Attributes\Route;

/**
 * Contrôleur de suppression de compte utilisateur
 *
 * Gère la suppression du profil utilisateur : affiche la confirmation (GET)
 * et effectue la suppression avec déconnexion (POST).
 */
#[Route('/user/delete', name: 'user_delete')]
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
            redirect(url('user_login'));
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
        // OWASP A01 : vérification CSRF
        $this->csrfVerify();

        if (!isset($_SESSION['user_id'])) {
            redirect(url('user_login'));
            return;
        }

        // OWASP A09 : log suppression de compte
        \Helpers\SecurityLogger::log('ACCOUNT_DELETED', ['user_id' => $_SESSION['user_id']]);

        $userModel = new User();
        $userModel->deleteProfil($_SESSION['user_id']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_pseudo']);
        session_destroy();
        redirect(url('homepage'));
    }
}
