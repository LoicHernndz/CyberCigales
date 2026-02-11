<?php
namespace Controllers;

use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;

class Email extends AbstractController
{
    function getMethod()
    {
        $view = new InterfaceMailView();
        $model = new InterfaceMailModel();
        $emails = $model->getemail();
        $view->render($emails);
    }
}
