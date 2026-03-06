<?php

namespace Controllers\Game;

use Controllers\AbstractController;

/**
 * Contrôleur du jeu de Phishing
 *
 * Redirige vers l'interface email en mode jeu pour permettre
 * à l'utilisateur d'apprendre à identifier les emails de phishing.
 */
class Phishing extends AbstractController
{
    /**
     * Redirige vers l'interface email en mode jeu de phishing
     *
     * @return void
     */
    public function getMethod(): void
    {
        header('Location: /email?mode=game');
        exit;
    }
}
