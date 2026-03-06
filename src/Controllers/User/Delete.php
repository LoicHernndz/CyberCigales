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
 *
 * OWASP A01 - Broken Access Control : vérification CSRF et authentification sur POST
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
     * OWASP A01 : on vérifie le CSRF et l'authentification avant toute action destructive
     *
     * @return void
     */
    public function postMethod(): void
    {
        // OWASP A01 - Broken Access Control : vérification du token CSRF
        $this->csrfVerify();

        // OWASP A07 - Identification and Authentication Failures :
        // on vérifie que l'utilisateur est bien connecté avant de supprimer son compte
        if (!isset($_SESSION['user_id'])) {
            redirect('/user/login');
            return;
        }

        // OWASP A09 - Logging : on log la suppression de compte avant de détruire la session
        \helpers\SecurityLogger::log('ACCOUNT_DELETED', ['user_id' => $_SESSION['user_id']]);

        $userModel = new User();
        $userModel->deleteProfil($_SESSION['user_id']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_pseudo']);
        session_destroy();
        redirect("/");
    }
}
