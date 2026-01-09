<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconCesar\LeconCesarView;
use Controllers\AbstractController;

class LeconCesar extends AbstractController {

    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconCesarView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
