<?php
namespace Controllers;

use Views\Mentions\MentionsView;
use Attributes\Route;

/**
 * Contrôleur de la page des mentions légales
 * 
 * Affiche les mentions légales et informations légales du site.
 */
#[Route('/mentions', name: 'mentions')]
class Mentions extends AbstractController
{
    /**
     * Affiche la page des mentions légales
     * 
     * @return void
     */
    function getMethod(){
        // Création d’une instance de la vue "MentionsView"
        $view = new MentionsView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
