<?php
namespace Controllers\InterfaceMail;

use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;
use Controllers\AbstractController;

/**
 * Contrôleur de l'interface email simulée
 * 
 * Affiche une boîte mail avec les emails récupérés depuis le modèle.
 */
class InterfaceMail extends AbstractController
{
    /**
     * Affiche l'interface email
     * 
     * Récupère les emails depuis le modèle et les passe à la vue.
     * 
     * @return void
     */
    function getMethod()
    {
        $view = new InterfaceMailView();
        $model = new InterfaceMailModel();

        // 1. Récupérer les données
        $emails = $model->getemail();

        // 2. Passer les données à la vue
        $view->render($emails);
    }
}