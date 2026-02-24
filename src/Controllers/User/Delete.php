<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Views\Homepage\HomepageView;
use Models\User\User;

class Delete extends AbstractController
{
    function getMethod()
    {
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect('/user/login');
            return;
        }
        $view = new HomepageView();
        $view->render();
    }

    function postMethod()
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
