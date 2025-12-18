<?php
namespace Controllers\InterfaceAgenda;

use Views\InterfaceAgenda\InterfaceAgendaView;
use Controllers\AbstractController;

class InterfaceAgenda extends AbstractController
{
    function getMethod(){
        $view = new InterfaceAgendaView();
        $view->render();
    }
}