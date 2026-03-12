<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use Services\BashSimulator;
use Attributes\Route;

/**
 * Contrôleur d'exécution de commandes Bash
 *
 * Gère l'exécution des commandes soumises dans le terminal simulé
 * en déléguant le traitement au service BashSimulator.
 */
#[Route('/bash/exec', name: 'bash_exec')]
class Exec extends AbstractController
{
    /**
     * Traite une requête d'exécution de commande bash
     *
     * @return void
     */
    public function getMethod(): void
    {
        $helper = new BashSimulator();
        $helper->control();
    }
}
