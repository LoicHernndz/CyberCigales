<?php

namespace Models\Chat;

use config\Database;

/**
 * Modèle pour gérer la progression des utilisateurs dans les chats
 */
class UserChatProgress
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Récupère l'indice de progression d'un utilisateur pour un chat
     * @param int $userId
     * @param string $chatName
     * @return int L'indice de progression (0 par défaut)
     */
    public function getProgressIndex(int $userId, string $chatName): int
    {
        $this->db->query('SELECT progress_index FROM user_chat_progress 
                          WHERE user_id = :user_id AND chat_name = :chat_name');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        $result = $this->db->single();
        
        return $result ? (int) $result->progress_index : 0;
    }

    /**
     * Incrémente l'indice de progression d'un utilisateur pour un chat
     * @param int $userId
     * @param string $chatName
     * @return bool
     */
    public function incrementProgress(int $userId, string $chatName): bool
    {
        // INSERT ... ON DUPLICATE KEY UPDATE pour insérer ou mettre à jour
        $this->db->query('INSERT INTO user_chat_progress (user_id, chat_name, progress_index)
                          VALUES (:user_id, :chat_name, 1)
                          ON DUPLICATE KEY UPDATE progress_index = progress_index + 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        
        return $this->db->execute();
    }

    /**
     * Définit un indice de progression spécifique
     * @param int $userId
     * @param string $chatName
     * @param int $index
     * @return bool
     */
    public function setProgressIndex(int $userId, string $chatName, int $index): bool
    {
        $this->db->query('INSERT INTO user_chat_progress (user_id, chat_name, progress_index)
                          VALUES (:user_id, :chat_name, :progress_index)
                          ON DUPLICATE KEY UPDATE progress_index = :progress_index2');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        $this->db->bind(':progress_index', $index);
        $this->db->bind(':progress_index2', $index);
        
        return $this->db->execute();
    }

    /**
     * Réinitialise la progression d'un utilisateur pour un chat
     * @param int $userId
     * @param string $chatName
     * @return bool
     */
    public function resetProgress(int $userId, string $chatName): bool
    {
        $this->db->query('DELETE FROM user_chat_progress 
                          WHERE user_id = :user_id AND chat_name = :chat_name');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        
        return $this->db->execute();
    }

    /**
     * Récupère toutes les progressions d'un utilisateur
     * @param int $userId
     * @return array
     */
    public function getAllUserProgress(int $userId): array
    {
        $this->db->query('SELECT chat_name, progress_index, updated_at 
                          FROM user_chat_progress 
                          WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
}

