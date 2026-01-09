<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconHistMdp\LeconHistMdpView;
use Controllers\AbstractController;

class LeconHistMdp extends AbstractController {

    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconHistMdpView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
