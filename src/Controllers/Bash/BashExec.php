<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use helpers\BashRequest;

/**
 * Contrôleur pour l'exécution des commandes Bash via AJAX
 */
class BashExec extends AbstractController
{
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

