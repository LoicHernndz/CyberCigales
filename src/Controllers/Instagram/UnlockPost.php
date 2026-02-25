<?php

namespace Controllers\Instagram;

use Controllers\AbstractController;

/**
 * Class UnlockPost
 * 
 * Contrôleur appelé via AJAX pour débloquer un post Instagram
 * spécifique après avoir accompli une action (ex: lire le journal).
 */
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

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    function postMethod()
    {
        $this->getMethod();
    }
}
