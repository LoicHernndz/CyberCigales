<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\PhishingGame\PhishingGameView;

class PhishingGame extends AbstractController
{
    public function getMethod()
    {
        // Redirection vers l'application Mail en mode mini-jeu
        header('Location: /email?mode=game');
        exit;
    }
}
