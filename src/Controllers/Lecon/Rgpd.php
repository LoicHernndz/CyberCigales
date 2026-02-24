<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\RgpdPresentation\RgpdPresentationView;

class Rgpd extends AbstractController
{
    function getMethod()
    {
        if (!isset($_SESSION['user_id'])) {
            flash('qcm', 'Vous devez être connecté pour accéder au contenu RGPD.', 'form-message form-message-red');
            redirect('/user/login');
        }
        $view = new RgpdPresentationView();
        $view->render();
    }
}
