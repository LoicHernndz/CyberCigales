<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use helpers\BashRequest;
use Attributes\Route;

/**
 * Contrôleur d'exécution de commandes Bash
 *
 * Gère l'exécution des commandes soumises dans le terminal simulé
 * en déléguant le traitement au helper BashRequest.
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
        $helper = new BashRequest();
        $helper->control();
    }
}
