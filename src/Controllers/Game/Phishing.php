<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Attributes\Route;

/**
 * Contrôleur du jeu de Phishing
 *
 * Redirige vers l'interface email en mode jeu pour permettre
 * à l'utilisateur d'apprendre à identifier les emails de phishing.
 */
#[Route('/game/phishing', name: 'game_phishing')]
class Phishing extends AbstractController
{
    /**
     * Redirige vers l'interface email en mode jeu de phishing
     *
     * @return void
     */
    public function getMethod(): void
    {
        redirect(url('email') . '?mode=game');
    }
}
