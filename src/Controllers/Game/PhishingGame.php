<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\PhishingGame\PhishingGameView;

/**
 * Contrôleur du jeu de détection de phishing
 * 
 * Jeu éducatif pour apprendre à identifier les emails frauduleux.
 */
class PhishingGame extends AbstractController
{
    /**
     * Affiche la page du jeu de détection de phishing
     * 
     * @return void
     */
    public function getMethod()
    {
        // Redirection vers l'application Mail en mode mini-jeu
        header('Location: /email?mode=game');
        exit;
    }
}
