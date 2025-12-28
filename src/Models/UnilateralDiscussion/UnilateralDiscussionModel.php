<?php
namespace Models\UnilateralDiscussion;

use config\Database;

/**
 * Modèle pour la discussion unilatérale (diffuseur -> récepteur)
 * Pas d'historique : une seule ligne par paire (sender_id, receiver_id)
 * La ligne est supprimée lors de la déconnexion
 */
class UnilateralDiscussionModel
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->createTableIfNotExists();
    }
    
    /**
     * Crée la table si elle n'existe pas
     * Clé primaire composite : (sender_id, receiver_id)
     */
    private function createTableIfNotExists(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS unilateral_discussions (
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            last_message TEXT NOT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (sender_id, receiver_id),
            INDEX idx_receiver (receiver_id),
            INDEX idx_updated (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
    }
    
    /**
     * Envoie un message (écrase le précédent)
     * 
     * @param int $senderId ID du diffuseur
     * @param int $receiverId ID du récepteur
     * @param string $message Message à envoyer
     * @return bool Succès de l'opération
     */
    public function sendMessage(int $senderId, int $receiverId, string $message): bool
    {
        try {
            // INSERT ... ON DUPLICATE KEY UPDATE pour écraser le message précédent
            $query = "INSERT INTO unilateral_discussions (sender_id, receiver_id, last_message, updated_at)
                      VALUES (:sender_id, :receiver_id, :message, NOW())
                      ON DUPLICATE KEY UPDATE 
                      last_message = :message_update,
                      updated_at = NOW()";
            
            $this->db->query($query);
            $this->db->bind(':sender_id', $senderId);
            $this->db->bind(':receiver_id', $receiverId);
            $this->db->bind(':message', $message);
            $this->db->bind(':message_update', $message);
            
            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Erreur envoi message unilatéral : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère le dernier message pour un récepteur
     * 
     * @param int $receiverId ID du récepteur
     * @return array|null Message ou null si aucun
     */
    public function getLastMessage(int $receiverId): ?array
    {
        try {
            $query = "SELECT sender_id, receiver_id, last_message, updated_at
                      FROM unilateral_discussions
                      WHERE receiver_id = :receiver_id
                      ORDER BY updated_at DESC
                      LIMIT 1";
            
            $this->db->query($query);
            $this->db->bind(':receiver_id', $receiverId);
            $result = $this->db->single();
            
            if ($result) {
                return [
                    'sender_id' => (int)$result->sender_id,
                    'receiver_id' => (int)$result->receiver_id,
                    'message' => $result->last_message,
                    'updated_at' => $result->updated_at
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Erreur récupération message unilatéral : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Supprime la discussion (lors de la déconnexion)
     * 
     * @param int $senderId ID du diffuseur
     * @param int $receiverId ID du récepteur
     * @return bool Succès de l'opération
     */
    public function disconnect(int $senderId, int $receiverId): bool
    {
        try {
            $query = "DELETE FROM unilateral_discussions
                      WHERE sender_id = :sender_id AND receiver_id = :receiver_id";
            
            $this->db->query($query);
            $this->db->bind(':sender_id', $senderId);
            $this->db->bind(':receiver_id', $receiverId);
            
            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Erreur déconnexion discussion unilatérale : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime toutes les discussions d'un utilisateur (déconnexion complète)
     * 
     * @param int $userId ID de l'utilisateur (en tant que diffuseur)
     * @return bool Succès de l'opération
     */
    public function disconnectAll(int $userId): bool
    {
        try {
            $query = "DELETE FROM unilateral_discussions
                      WHERE sender_id = :user_id";
            
            $this->db->query($query);
            $this->db->bind(':user_id', $userId);
            
            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Erreur déconnexion complète : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si un message existe pour un récepteur
     * 
     * @param int $receiverId ID du récepteur
     * @return bool True si un message existe
     */
    public function hasMessage(int $receiverId): bool
    {
        try {
            $query = "SELECT COUNT(*) as count
                      FROM unilateral_discussions
                      WHERE receiver_id = :receiver_id";
            
            $this->db->query($query);
            $this->db->bind(':receiver_id', $receiverId);
            $result = $this->db->single();
            
            return $result && $result->count > 0;
        } catch (\Exception $e) {
            error_log("Erreur vérification message : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère le nom d'utilisateur par ID
     * 
     * @param int $userId ID de l'utilisateur
     * @return string|null Nom d'utilisateur ou null
     */
    public function getUsername(int $userId): ?string
    {
        try {
            $query = "SELECT pseudo FROM users WHERE id = :user_id";
            $this->db->query($query);
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            return $result ? $result->pseudo : null;
        } catch (\Exception $e) {
            error_log("Erreur récupération username : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Recherche un utilisateur par pseudo et retourne son ID
     * 
     * @param string $pseudo Pseudo à rechercher
     * @return array|null Informations de l'utilisateur (id, pseudo, prenom, nom) ou null
     */
    public function findUserByPseudo(string $pseudo): ?array
    {
        try {
            $query = "SELECT id, pseudo, prenom, nom, email FROM users WHERE pseudo = :pseudo LIMIT 1";
            $this->db->query($query);
            $this->db->bind(':pseudo', $pseudo);
            $result = $this->db->single();
            
            if ($result) {
                return [
                    'id' => (int)$result->id,
                    'pseudo' => $result->pseudo,
                    'prenom' => $result->prenom ?? '',
                    'nom' => $result->nom ?? '',
                    'email' => $result->email ?? ''
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Erreur recherche utilisateur par pseudo : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Recherche des utilisateurs par pseudo (recherche partielle)
     * 
     * @param string $search Terme de recherche
     * @param int $limit Nombre maximum de résultats
     * @return array Liste des utilisateurs trouvés
     */
    public function searchUsersByPseudo(string $search, int $limit = 10): array
    {
        try {
            $query = "SELECT id, pseudo, prenom, nom FROM users 
                      WHERE pseudo LIKE :search 
                      ORDER BY pseudo ASC 
                      LIMIT :limit";
            $this->db->query($query);
            $this->db->bind(':search', '%' . $search . '%');
            $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
            $results = $this->db->resultSet();
            
            $users = [];
            foreach ($results as $result) {
                $users[] = [
                    'id' => (int)$result->id,
                    'pseudo' => $result->pseudo,
                    'prenom' => $result->prenom ?? '',
                    'nom' => $result->nom ?? ''
                ];
            }
            
            return $users;
        } catch (\Exception $e) {
            error_log("Erreur recherche utilisateurs : " . $e->getMessage());
            return [];
        }
    }
}

