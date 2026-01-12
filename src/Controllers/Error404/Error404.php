<?php

namespace Controllers\Error404;

use Controllers\AbstractController;
use Views\Page404\Page404View;

/**
 * Contrôleur de la page d'erreur 404
 * 
 * Affiche une page d'erreur personnalisée lorsqu'une ressource n'est pas trouvée.
 */
class Error404 extends AbstractController {

    /**
     * Affiche la page d'erreur 404
     * 
     * @return void
     */
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new Page404View();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
