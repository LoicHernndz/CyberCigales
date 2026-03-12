<?php

namespace Controllers\Instagram\Chat;

use Controllers\AbstractController;
use Services\ChatResponseService;
use Attributes\Route;

/**
 * Contrôleur des réponses du chat Instagram
 *
 * Délègue la génération des réponses au helper GenerateAnswer,
 * aussi bien en GET qu'en POST.
 */
#[Route('/instagram/chat/response', name: 'instagram_chat_response')]
class Response extends AbstractController
{
    /**
     * Génère une réponse de chat (GET)
     *
     * @return void
     */
    public function getMethod(): void
    {
        $helper = new ChatResponseService();
        $helper->control();
    }

    /**
     * Génère une réponse de chat (POST)
     *
     * @return void
     */
    public function postMethod(): void
    {
        $helper = new ChatResponseService();
        $helper->control();
    }
}
