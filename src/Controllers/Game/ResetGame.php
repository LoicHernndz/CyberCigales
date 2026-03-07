<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Models\Game\ResetProgress;
use Attributes\Route;

/**
 * Contrôleur pour réinitialiser la progression de l'escape game
 */
#[Route('/game/reset-game', name: 'game_reset_game')]
class ResetGame extends AbstractController
{
    /**
     * Empêche l'accès GET direct à la route de réinitialisation
     *
     * @return void
     */
    public function getMethod(): void
    {
        // Rediriger vers le profil si quelqu'un essaie d'accéder via GET
        header('Location: /user/profil');
        exit();
    }

    /**
     * Traite la demande de réinitialisation (doit être appelée via POST)
     *
     * @return void
     */
    public function postMethod(): void
    {
        // 1. Vérifier si l'utilisateur est connecté
        $this->connexionVerify();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /user/login');
            exit();
        }

        // 2. Vérifier l'intention depuis le formulaire
        $action = $_POST['action'] ?? '';
        if ($action !== 'reset_all') {
            header('Location: /user/profil');
            exit();
        }

        // 3. Réinitialiser la base de données
        $resetModel = new ResetProgress();
        $success = $resetModel->resetAllProgress($userId);

        if ($success) {
            // 4. Réinitialiser les données en session

            // Hamming Game
            unset($_SESSION['hamming_streak']);
            unset($_SESSION['hamming_square']);
            unset($_SESSION['hamming_original']);
            unset($_SESSION['hamming_error_pos']);

            // Frequency Game
            unset($_SESSION['freq_game_solution']);
            unset($_SESSION['freq_game_start']);

            // Cypher Game
            unset($_SESSION['cypher_game_started']);
            unset($_SESSION['cypher_current_challenge']);
            unset($_SESSION['cypher_start_time']);
            unset($_SESSION['cypher_hints_used']);
            unset($_SESSION['cypher_score']);

            // Définir un message de succès (à gérer dans la vue si un jour supporté)
            $_SESSION['flash_message'] = 'Votre progression a été réinitialisée avec succès.';
        } else {
            $_SESSION['flash_error'] = 'Une erreur est survenue lors de la réinitialisation.';
        }

        // 5. Rediriger vers le profil
        header('Location: /user/profil');
        exit();
    }
}
