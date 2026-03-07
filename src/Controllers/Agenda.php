<?php

namespace Controllers;

use Views\InterfaceAgenda\InterfaceAgendaView;
use Attributes\Route;

/**
 * Contrôleur de la page Agenda
 *
 * Affiche l'interface de l'agenda via la vue correspondante.
 */
#[Route('/agenda', name: 'agenda')]
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
