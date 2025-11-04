<?php

namespace Controllers\LeconCode;

use Views\LeconChiffrement\LeconVigenereView;
use Controllers\AbstractController;

class LeconVigenere extends AbstractController {

    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconVigenereView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
