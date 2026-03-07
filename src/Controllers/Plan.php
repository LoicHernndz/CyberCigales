<?php

namespace Controllers;

use Views\SitePlan\SitePlanView;
use Attributes\Route;

/**
 * Contrôleur de la page Plan du site
 *
 * Affiche le plan du site via la vue correspondante.
 */
#[Route('/plan', name: 'plan')]
class Plan extends AbstractController
{
    /**
     * Affiche le plan du site
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new SitePlanView();
        $view->render();
    }
}
