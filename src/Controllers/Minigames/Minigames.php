<?php

namespace Controllers\Minigames;

use Views\Minigames\MinigamesView;
use Controllers\AbstractController;

/**
 * Contrôleur de la liste des mini-jeux
 * 
 * Affiche la page listant tous les mini-jeux disponibles.
 */
class Minigames extends AbstractController {

    /**
     * Affiche la liste des mini-jeux
     * 
     * @return void
     */
    public function getMethod() {
        $view = new MinigamesView();
        $view->render();
    }
}
