<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconCesar\LeconCesarView;
use Controllers\AbstractController;

/**
 * Contrôleur de la leçon sur le chiffrement de César
 */
class LeconCesar extends AbstractController {

    /**
     * Affiche la leçon César
     * 
     * @return void
     */
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconCesarView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
