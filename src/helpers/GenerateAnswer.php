<?php

namespace helpers;

use config\Database;

class GenerateAnswer
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function control()
    {
        $name = $_REQUEST["name"];
        $step = $_REQUEST["step"];
        $message = $_REQUEST["message"];
        $userId = $_SESSION['user_id'] ?? null; // Récupère l'ID utilisateur de la session

        $this->generate($name, $step, $message, $userId);
    }

    private function generate($name, $step, $message, $userId): void
    {
        // Charger les données depuis answers.json
        $path = __DIR__ . '/../config/answers.json';
        $string = file_get_contents($path);
        $json_a = json_decode($string);

        // Vérifier si l'utilisateur existe dans le JSON
        if (!isset($json_a->$name)) {
            echo "Utilisateur non trouvé";
            echo "0";
            return;
        }

        // Vérifier si l'étape existe pour cet utilisateur
        if (!isset($json_a->$name->$step)) {
            echo "Étape non trouvée";
            echo "0";
            return;
        }

        // Si le message est vide, renvoyer le message de l'étape actuelle
        if ($message == "") {
            echo $json_a->$name->$step->{"message"};
            echo "0";
        }
        // Si l'étape a une clé et que le message contient cette clé
        else if (isset($json_a->$name->$step->{"key"}) && str_contains($message, $json_a->$name->$step->{"key"})) {
            $next_step = strval((int)($step + 1));
            
            // Vérifier si l'étape suivante existe
            if (!isset($json_a->$name->$next_step)) {
                echo "Conversation terminée";
                echo "0";
                return;
            }
            
            echo $json_a->$name->$next_step->{"message"};
            echo "1";
            
            // Sauvegarder la progression dans la BDD si l'utilisateur est connecté
            if ($userId) {
                $this->saveProgress($userId, $name, (int)$next_step);
            }
        } 
        // Sinon, renvoyer une réponse aléatoire
        else {
            $responses = $json_a->$name->$step->{"responses"};
            echo $responses[array_rand($responses)];
            echo "0";
        }
    }

    /**
     * Sauvegarde ou met à jour la progression de l'utilisateur
     */
    private function saveProgress($userId, $chatName, $progressIndex): void
    {
        // Vérifier si une progression existe déjà
        $this->db->query('SELECT id FROM user_chat_progress WHERE user_id = :user_id AND chat_name = :chat_name');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        $existing = $this->db->single();

        if ($existing) {
            // Mettre à jour la progression
            $this->db->query('UPDATE user_chat_progress SET progress_index = :progress_index WHERE user_id = :user_id AND chat_name = :chat_name');
            $this->db->bind(':progress_index', $progressIndex);
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':chat_name', $chatName);
            $this->db->execute();
        } else {
            // Créer une nouvelle entrée
            $this->db->query('INSERT INTO user_chat_progress (user_id, chat_name, progress_index) VALUES (:user_id, :chat_name, :progress_index)');
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':chat_name', $chatName);
            $this->db->bind(':progress_index', $progressIndex);
            $this->db->execute();
        }
    }

    /**
     * Récupère la progression de l'utilisateur
     */
    public function getProgress($userId, $chatName): int
    {
        $this->db->query('SELECT progress_index FROM user_chat_progress WHERE user_id = :user_id AND chat_name = :chat_name');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        $result = $this->db->single();
        
        return $result ? (int)$result->progress_index : 0;
    }

    /**
     * Réinitialise la progression de l'utilisateur
     */
    public function resetProgress($userId, $chatName): void
    {
        $this->db->query('DELETE FROM user_chat_progress WHERE user_id = :user_id AND chat_name = :chat_name');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':chat_name', $chatName);
        $this->db->execute();
    }
}