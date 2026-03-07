<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconHistMdp\LeconHistMdpView;
use Attributes\Route;

/**
 * Contrôleur de la leçon sur l'histoire des mots de passe
 *
 * Affiche le contenu pédagogique retraçant l'évolution
 * et l'importance des mots de passe en cybersécurité.
 */
#[Route('/lecon/hist-mdp', name: 'lecon_hist_mdp')]
class HistMdp extends AbstractController
{
    /**
     * Affiche la leçon sur l'histoire des mots de passe
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new LeconHistMdpView();
        $view->render();
    }
}
