<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Views\Homepage\HomepageView;
use Models\User\User;
use Views\User\DeleteProfil\DeleteProfilView;

/**
 * Contrôleur de suppression de compte utilisateur
 * 
 * Gère la suppression définitive du compte utilisateur et de toutes ses données.
 */
class DeleteProfil extends AbstractController
{
    /**
     * Affiche la page de confirmation de suppression
     * 
     * Redirige vers login si l'utilisateur n'est pas connecté.
     * 
     * @return void
     */
    function getMethod()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect('/user/login');
            return;
        }

        // Afficher le formulaire d'édition
        $view = new HomepageView();
        $view->render();
    }

    /**
     * Exécute la suppression du compte utilisateur
     * 
     * Supprime le compte en base de données, détruit la session
     * et redirige vers la page d'accueil.
     * 
     * @return void
     */
    function postMethod()
    {
        $userModel = new User();
        $userModel->deleteProfil($_SESSION['user_id']);

        // Je supprime la variable de session qui contient l'id de l'utilisateur
        unset($_SESSION['user_id']);
        // Je supprime la variable de session qui contient l'email de l'utilisateur
        unset($_SESSION['user_email']);
        // Je supprime la variable de session qui contient le pseudo de l'utilisateur
        unset($_SESSION['user_pseudo']);

        session_destroy();
        redirect("/");

        // Afficher la page d'accueil
        $view = new HomepageView();
        $view->render();
    }
}

