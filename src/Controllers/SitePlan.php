<?php
namespace Controllers;

use Views\SitePlan\SitePlanView;
use Attributes\Route;

/**
 * Contrôleur du plan du site
 * 
 * Affiche une vue d'ensemble de la structure et navigation du site.
 */
#[Route('/plan', name: 'site_plan')]
class SitePlan extends AbstractController
{
    /**
     * Affiche la page du plan du site
     * 
     * @return void
     */
    function getMethod(){
        // Création d’une instance de la vue "SitePlanView"
        $view = new SitePlanView();
        // Affichage du contenu de la page "Plan du site"
        $view->render();
    }
}
