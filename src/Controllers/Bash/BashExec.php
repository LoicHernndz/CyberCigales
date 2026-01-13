<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use helpers\BashRequest;

/**
 * Contrôleur pour l'exécution des commandes Bash via AJAX
 */
class BashExec extends AbstractController
{
    /**
     * Traite les requêtes de commandes Bash
     * 
     * Délègue le traitement au helper BashRequest.
     * 
     * @return void
     */
    public function getMethod(): void
    {
        $helper = new BashRequest();
        $helper->control();
    }

    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}

