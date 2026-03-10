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
        // OWASP A05 : retourner le vrai code HTTP 404 (au lieu de 200)
        http_response_code(404);
        $view = new Page404View();
        $view->render();
    }
}
