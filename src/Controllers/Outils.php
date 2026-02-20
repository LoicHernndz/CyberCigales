<?php

namespace Controllers;

use Views\Code\AllCodePage\AllCodePageView;

/**
 * Contrôleur de la page Outils
 *
 * Affiche la page listant tous les outils de chiffrement/déchiffrement disponibles.
 */
class Outils extends AbstractController
{
    /**
     * Affiche la page de tous les outils cryptographiques
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new AllCodePageView();
        $view->render();
    }
}
