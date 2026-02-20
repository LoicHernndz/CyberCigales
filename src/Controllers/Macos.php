<?php

namespace Controllers;

use Views\MacOSView\MacOSView;

/**
 * Contrôleur de la page MacOS
 *
 * Affiche l'interface simulée de MacOS via la vue correspondante.
 */
class Macos extends AbstractController
{
    /**
     * Affiche l'interface MacOS simulée
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new MacOSView();
        $view->render();
    }
}
