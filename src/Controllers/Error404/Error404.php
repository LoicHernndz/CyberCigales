<?php

namespace Controllers\Error404;

use Controllers\AbstractController;
use Views\Page404\Page404View;
use Attributes\Route;

/**
 * Contrôleur de la page d'erreur 404
 *
 * Affiche une page d'erreur personnalisée lorsqu'une ressource n'est pas trouvée.
 */
#[Route('/error404', name: 'error404')]
class Error404 extends AbstractController {

    /**
     * Affiche la page d'erreur 404
     * 
     * @return void
     */
    function getMethod(){
        http_response_code(404);
        $view = new Page404View();
        $view->render();
    }
}
