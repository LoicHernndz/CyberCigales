<?php

namespace Models\Game;

use config\Database;

/**
 * Modèle pour gérer la réinitialisation de la progression de l'escape game
 */
class ResetProgress
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Réinitialise toute la progression de l'escape game pour un utilisateur donné.
     * 
     * @param int $userId L'ID de l'utilisateur concerné
     * @return bool True si la réinitialisation a réussi, False sinon
     */
    public function resetAllProgress(int $userId): bool
    {
        try {
            // 1. Supprimer la progression des chats Instagram
            $this->db->query('DELETE FROM user_chat_progress WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            $this->db->execute();

            // 2. Supprimer les scores Cypher Rush
            $tryCypher = true;
            try {
                $this->db->query('DELETE FROM cypher_scores WHERE user_id = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            } catch (\Exception $e) {
                // La table n'existe peut-être pas encore
                $tryCypher = false;
            }

            // 3. Supprimer la progression des leçons
            try {
                $this->db->query('DELETE FROM lesson_progress WHERE user_id = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            } catch (\Exception $e) {
                // La table n'existe peut-être pas encore
            }

            // 4. Réinitialiser le score global de l'utilisateur
            $this->db->query('UPDATE users SET score = 0 WHERE id = :user_id');
            $this->db->bind(':user_id', $userId);
            $this->db->execute();

            // 4. Réinitialiser les variables de session liées au jeu
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['instagram_mel_post_unlocked']);

            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la réinitialisation de la progression : " . $e->getMessage());
            return false;
        }
    }
}
