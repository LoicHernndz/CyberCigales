<?php

namespace Controllers\Minigames;

use Controllers\AbstractController;
use Views\Minigames\MinigamesView;
use Attributes\Route;

/**
 * Contrôleur de la page d'accueil des mini-jeux
 *
 * Affiche la liste de tous les mini-jeux éducatifs disponibles.
 * Utilise la convention Index pour le routeur dynamique (/minigames → Minigames\Index).
 */
#[Route('/minigames', name: 'minigames')]
class Index extends AbstractController
{
    /**
     * Affiche la page listant tous les mini-jeux disponibles
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new MinigamesView();
        $view->render();
    }
}
