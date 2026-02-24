<?php

namespace Controllers\Instagram\Chat;

use Controllers\AbstractController;
use helpers\GenerateAnswer;

/**
 * Contrôleur des réponses du chat Instagram
 *
 * Délègue la génération des réponses au helper GenerateAnswer,
 * aussi bien en GET qu'en POST.
 */
class Response extends AbstractController
{
    /**
     * Génère une réponse de chat (GET)
     *
     * @return void
     */
    public function getMethod(): void
    {
        $helper = new GenerateAnswer();
        $helper->control();
    }

    /**
     * Génère une réponse de chat (POST)
     *
     * @return void
     */
    public function postMethod(): void
    {
        $helper = new GenerateAnswer();
        $helper->control();
    }
}
