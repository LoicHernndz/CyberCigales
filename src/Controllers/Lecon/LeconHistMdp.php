<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconHistMdp\LeconHistMdpView;
use Controllers\AbstractController;

/**
 * Contrôleur de la leçon sur l'historique des mots de passe
 * 
 * Affiche la leçon pédagogique sur l'évolution des mots de passe.
 */
class LeconHistMdp extends AbstractController
{

    /**
     * Affiche la leçon sur l'historique des mots de passe
     * 
     * @return void
     */
    function getMethod()
    {
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconHistMdpView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
