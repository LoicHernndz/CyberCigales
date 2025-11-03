<?php
namespace Controllers;

use Views\Mentions\MentionsView;

class Mentions extends AbstractController
{
    // Méthode principale exécutée quand la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new MentionsView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}



