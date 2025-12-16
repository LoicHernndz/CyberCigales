<?php
namespace Controllers\InterfaceMail;

use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;
use Controllers\AbstractController;

class InterfaceMail extends AbstractController
{
    function getMethod(){
        $view = new InterfaceMailView();
        $model = new InterfaceMailModel();

        // 1. Récupérer les données
        $emails = $model->getemail();

        // 2. Passer les données à la vue
        $view->render($emails);
    }
}