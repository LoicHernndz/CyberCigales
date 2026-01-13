<?php
namespace Controllers\InterfaceAgenda;

use Views\InterfaceAgenda\InterfaceAgendaView;
use Controllers\AbstractController;

/**
 * Contrôleur de l'interface agenda simulée
 * 
 * Affiche un calendrier/agenda pour les activités pédagogiques.
 */
class InterfaceAgenda extends AbstractController
{
    /**
     * Affiche l'interface agenda
     * 
     * @return void
     */
    function getMethod()
    {
        $view = new InterfaceAgendaView();
        $view->render();
    }
}