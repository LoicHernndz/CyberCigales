<?php

namespace Controllers\Instagram;

use Controllers\AbstractController;
use Attributes\Route;

/**
 * Class UnlockPost
 *
 * Contrôleur appelé via AJAX pour débloquer un post Instagram
 * spécifique après avoir accompli une action (ex: lire le journal).
 */
#[Route('/instagram/unlock-post', name: 'instagram_unlock_post')]
class UnlockPost extends AbstractController
{
    function getMethod()
    {
        // On s'assure que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Débloquer le post
        $_SESSION['instagram_mel_post_unlocked'] = true;

        $this->jsonResponse(['success' => true]);
    }

    function postMethod()
    {
        $this->getMethod();
    }
}
