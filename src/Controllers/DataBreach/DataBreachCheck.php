<?php

namespace Controllers\DataBreach;

use Controllers\AbstractController;
use Views\DataBreach\DataBreachCheckView;

/**
 * Contrôleur du vérificateur de fuite de données
 * 
 * Affiche l'interface de vérification de présence d'email dans les fuites de données.
 */
class DataBreachCheck extends AbstractController
{
    /**
     * Affiche la page de vérification de fuite de données
     * 
     * @return void
     */
    function getMethod()
    {
        $view = new DataBreachCheckView();
        $view->render();
    }
}
