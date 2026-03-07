<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use Views\Bash\BashView;
use Attributes\Route;

/**
 * Contrôleur de la page d'accueil du terminal Bash
 *
 * Affiche l'interface du terminal bash simulé via la vue correspondante.
 * Utilise la convention Index pour le routeur dynamique (/bash → Bash\Index).
 */
#[Route('/bash', name: 'bash')]
class Index extends AbstractController
{
    /**
     * Affiche l'interface du terminal bash simulé
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new BashView();
        $view->render();
    }
}
