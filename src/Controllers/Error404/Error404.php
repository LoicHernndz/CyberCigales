<?php

namespace Controllers\Error404;

use Controllers\AbstractController;
use Views\Page404\Page404View;

class Error404 extends AbstractController {

    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new Page404View();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
