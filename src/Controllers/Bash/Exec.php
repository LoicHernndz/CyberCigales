<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use helpers\BashRequest;

/**
 * Contrôleur d'exécution de commandes Bash
 *
 * Gère l'exécution des commandes soumises dans le terminal simulé
 * en déléguant le traitement au helper BashRequest.
 */
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

    /**
     * Vérifie si la méthode HTTP est supportée par ce contrôleur
     *
     * @param string $method La méthode HTTP à vérifier (GET, POST, etc.)
     * @return bool True si la méthode est GET ou POST
     */
    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}
