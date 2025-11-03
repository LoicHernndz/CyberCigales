<?php

namespace Models;

use config\Database;

/**
 * Modèle pour gérer la progression de l'utilisateur dans le QCM RGPD
 */
class UserQcmProgress
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Enregistre une réponse utilisateur
     * @param int $userId
     * @param int $questionId
     * @param int $reponseId
     * @param bool $estCorrecte
     * @param int $pointsObtenus
     * @return bool
     */
    public function saveReponse(int $userId, int $questionId, int $reponseId, bool $estCorrecte, int $pointsObtenus): bool
    {
        // Vérifier si l'utilisateur a déjà répondu à cette question
        $existing = $this->getReponse($userId, $questionId);

        if ($existing) {
            // Mettre à jour la réponse existante
            $this->db->query('UPDATE user_qcm_progress 
                              SET reponse_id = :reponse_id,
                                  est_correcte = :est_correcte,
                                  points_obtenus = :points_obtenus,
                                  answered_at = NOW()
                              WHERE user_id = :user_id AND question_id = :question_id');
        } else {
            // Créer une nouvelle réponse
            $this->db->query('INSERT INTO user_qcm_progress 
                              (user_id, question_id, reponse_id, est_correcte, points_obtenus, answered_at)
                              VALUES (:user_id, :question_id, :reponse_id, :est_correcte, :points_obtenus, NOW())');
        }

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':question_id', $questionId);
        $this->db->bind(':reponse_id', $reponseId);
        $this->db->bind(':est_correcte', $estCorrecte ? 1 : 0);
        $this->db->bind(':points_obtenus', $pointsObtenus);

        return $this->db->execute();
    }

    /**
     * Récupère la réponse d'un utilisateur pour une question
     * @param int $userId
     * @param int $questionId
     * @return object|false
     */
    public function getReponse(int $userId, int $questionId)
    {
        $this->db->query('SELECT * FROM user_qcm_progress 
                          WHERE user_id = :user_id AND question_id = :question_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':question_id', $questionId);
        return $this->db->single();
    }

    /**
     * Vérifie si l'utilisateur a déjà répondu à une question
     * @param int $userId
     * @param int $questionId
     * @return bool
     */
    public function hasAnswered(int $userId, int $questionId): bool
    {
        return (bool)$this->getReponse($userId, $questionId);
    }

    /**
     * Récupère toutes les réponses d'un utilisateur
     * @param int $userId
     * @return array
     */
    public function getAllUserReponses(int $userId): array
    {
        $this->db->query('SELECT * FROM user_qcm_progress 
                          WHERE user_id = :user_id 
                          ORDER BY answered_at ASC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Calcule le score total d'un utilisateur pour le QCM
     * @param int $userId
     * @return int
     */
    public function getUserScore(int $userId): int
    {
        $this->db->query('SELECT COALESCE(SUM(points_obtenus), 0) as total_score 
                          FROM user_qcm_progress 
                          WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return (int)$result->total_score;
    }

    /**
     * Compte le nombre de bonnes réponses d'un utilisateur
     * @param int $userId
     * @return int
     */
    public function countCorrectAnswers(int $userId): int
    {
        $this->db->query('SELECT COUNT(*) as total 
                          FROM user_qcm_progress 
                          WHERE user_id = :user_id AND est_correcte = 1');
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return (int)$result->total;
    }

    /**
     * Compte le nombre total de réponses d'un utilisateur
     * @param int $userId
     * @return int
     */
    public function countTotalAnswers(int $userId): int
    {
        $this->db->query('SELECT COUNT(*) as total 
                          FROM user_qcm_progress 
                          WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return (int)$result->total;
    }

    /**
     * Calcule le pourcentage de réussite
     * @param int $userId
     * @return float
     */
    public function getSuccessRate(int $userId): float
    {
        $total = $this->countTotalAnswers($userId);
        if ($total === 0) {
            return 0.0;
        }
        $correct = $this->countCorrectAnswers($userId);
        return round(($correct / $total) * 100, 1);
    }

    /**
     * Réinitialise le QCM pour un utilisateur (supprime toutes ses réponses)
     * @param int $userId
     * @return bool
     */
    public function resetProgress(int $userId): bool
    {
        $this->db->query('DELETE FROM user_qcm_progress WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Récupère le classement des meilleurs scores (top 10)
     * @return array
     */
    public function getLeaderboard(int $limit = 10): array
    {
        $this->db->query('SELECT u.pseudo, 
                                 SUM(uqp.points_obtenus) as total_score,
                                 COUNT(CASE WHEN uqp.est_correcte = 1 THEN 1 END) as bonnes_reponses,
                                 COUNT(*) as total_reponses
                          FROM user_qcm_progress uqp
                          JOIN users u ON uqp.user_id = u.id
                          GROUP BY uqp.user_id
                          ORDER BY total_score DESC
                          LIMIT :limit');
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}

