<?php

namespace Models;

use config\Database;

/**
 * Modèle pour gérer les questions du QCM RGPD
 */
class QcmQuestion
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Récupère toutes les questions actives par ordre
     * @return array
     */
    public function getAllQuestions(): array
    {
        $this->db->query('SELECT * FROM qcm_questions WHERE est_active = 1 ORDER BY ordre ASC');
        return $this->db->resultSet();
    }

    /**
     * Récupère une question par son ID avec ses réponses
     * @param int $questionId
     * @return object|false
     */
    public function getQuestionWithReponses(int $questionId)
    {
        // Récupérer la question
        $this->db->query('SELECT * FROM qcm_questions WHERE id = :id AND est_active = 1');
        $this->db->bind(':id', $questionId);
        $question = $this->db->single();

        if (!$question) {
            return false;
        }

        // Récupérer les réponses
        $this->db->query('SELECT * FROM qcm_reponses WHERE question_id = :question_id ORDER BY ordre ASC');
        $this->db->bind(':question_id', $questionId);
        $question->reponses = $this->db->resultSet();

        return $question;
    }

    /**
     * Récupère le nombre total de questions actives
     * @return int
     */
    public function getTotalQuestions(): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM qcm_questions WHERE est_active = 1');
        $result = $this->db->single();
        return (int)$result->total;
    }

    /**
     * Récupère une question par son ordre
     * @param int $ordre
     * @return object|false
     */
    public function getQuestionByOrdre(int $ordre)
    {
        $this->db->query('SELECT * FROM qcm_questions WHERE ordre = :ordre AND est_active = 1');
        $this->db->bind(':ordre', $ordre);
        $question = $this->db->single();

        if ($question) {
            // Ajouter les réponses
            $this->db->query('SELECT * FROM qcm_reponses WHERE question_id = :question_id ORDER BY ordre ASC');
            $this->db->bind(':question_id', $question->id);
            $question->reponses = $this->db->resultSet();
        }

        return $question;
    }

    /**
     * Vérifie si une réponse est correcte
     * @param int $reponseId
     * @return bool
     */
    public function isReponseCorrect(int $reponseId): bool
    {
        $this->db->query('SELECT est_correcte FROM qcm_reponses WHERE id = :id');
        $this->db->bind(':id', $reponseId);
        $result = $this->db->single();
        return $result && (bool)$result->est_correcte;
    }

    /**
     * Récupère les points pour une question
     * @param int $questionId
     * @param bool $correct
     * @return int
     */
    public function getPoints(int $questionId, bool $correct): int
    {
        $this->db->query('SELECT points_correct, points_incorrect FROM qcm_questions WHERE id = :id');
        $this->db->bind(':id', $questionId);
        $result = $this->db->single();
        
        if (!$result) {
            return 0;
        }

        return $correct ? (int)$result->points_correct : (int)$result->points_incorrect;
    }

    /**
     * Récupère l'explication d'une question
     * @param int $questionId
     * @return string
     */
    public function getExplication(int $questionId): string
    {
        $this->db->query('SELECT explication FROM qcm_questions WHERE id = :id');
        $this->db->bind(':id', $questionId);
        $result = $this->db->single();
        return $result ? $result->explication : '';
    }

    /**
     * Récupère la question ID depuis une réponse ID
     * @param int $reponseId
     * @return int|false
     */
    public function getQuestionIdFromReponse(int $reponseId)
    {
        $this->db->query('SELECT question_id FROM qcm_reponses WHERE id = :id');
        $this->db->bind(':id', $reponseId);
        $result = $this->db->single();
        return $result ? (int)$result->question_id : false;
    }
}

