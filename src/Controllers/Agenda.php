<?php

namespace Controllers;

use Views\InterfaceAgenda\InterfaceAgendaView;

/**
 * Contrôleur de la page Agenda
 *
 * Affiche l'interface de l'agenda via la vue correspondante.
 */
class Agenda extends AbstractController
{
    /**
     * Affiche la page de l'agenda
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new InterfaceAgendaView();
        $view->render();
    }
}
