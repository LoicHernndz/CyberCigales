<?php
namespace Controllers;

use Views\SitePlan\SitePlanView;

class SitePlan extends AbstractController
{
    // Méthode principale exécutée lorsque la route correspond à ce contrôleur
    function getMethod(){
        // Création d’une instance de la vue "SitePlanView"
        $view = new SitePlanView();
        // Affichage du contenu de la page "Plan du site"
        $view->render();
    }
}
