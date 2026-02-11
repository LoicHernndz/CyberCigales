<?php
namespace Controllers;

use Views\InterfaceAgenda\InterfaceAgendaView;

class Agenda extends AbstractController
{
    function getMethod()
    {
        $view = new InterfaceAgendaView();
        $view->render();
    }
}
