<?php
namespace Controllers\InterfaceAgenda;

use Views\InterfaceAgenda\InterfaceAgendaView;
use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;
use Controllers\AbstractController;

class InterfaceAgenda extends AbstractController
{
    function getMethod(){
        $view = new InterfaceAgendaView();
        $view->render();
    }
}