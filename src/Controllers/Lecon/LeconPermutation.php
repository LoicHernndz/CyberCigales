<?php

namespace Controllers\Lecon;

use Views\LeconChiffrement\LeconPermutationView;
use Controllers\AbstractController;

class LeconPermutation extends AbstractController {

    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconPermutationView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
