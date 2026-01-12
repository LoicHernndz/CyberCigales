<?php

namespace helpers;

use config\Database;
/**
 * Générateur de réponses contextuelles
 *
 * Charge les réponses depuis answers.json et retourne la réponse appropriée
 * en fonction du nom, de l'étape et du message de l'utilisateur.
 */

class GenerateAnswer
{
    /**
     * Point d'entrée pour traiter les requêtes de génération de réponses
     *
     * Récupère les paramètres de la requête et appelle generate().
     *
     * @return void
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function control()
    {
        $name = $_REQUEST["name"];
        $message = $_REQUEST["message"];
        $userId = $_SESSION['user_id'] ?? null; // Récupère l'ID utilisateur de la session

        $this->generate($name, $message, $userId);
    }

    /**
     * Génère une réponse contextuelle depuis answers.json
     *
     * Si le message est vide, retourne le message de l'étape actuelle.
     * Si le message contient la clé attendue, passe à l'étape suivante.
     * Sinon, retourne une réponse aléatoire parmi les réponses disponibles.
     *
     * @param string $name Le nom du contexte (ex: nom d'un exercice)
     * @param string $message Le message de l'utilisateur
     * @param string $userId ID de l'utilisateur
     * @return void Affiche la réponse suivie d'un indicateur (0 ou 1)
     */
    private function generate($name, $message, $userId): void
    {
        // Charger les données depuis answers.json
        $path = __DIR__ . '/../config/answers.json';
        $string = file_get_contents($path);
        $json_a = json_decode($string);
        $step = $this->getProgress($userId, $name);

        // Mapper les identifiants Instagram vers les vrais noms
        $realName = $this->mapUsernameToRealName($name);

        // Vérifier si l'utilisateur existe dans le JSON
        if (!isset($json_a->$realName)) {
            echo "Utilisateur non trouvé";
            return;
        }

        // Vérifier si l'étape existe pour cet utilisateur
        if (!isset($json_a->$realName->$step)) {
            echo "Étape non trouvée";
            return;
        }

        // Si le message est vide, renvoyer le message de l'étape actuelle
        if ($message == "") {
            echo $json_a->$realName->$step->{"message"};
        }
        // Si l'étape a une clé et que le message contient cette clé
        else if (isset($json_a->$realName->$step->{"key"}) && str_contains($message, $json_a->$realName->$step->{"key"})) {
            $next_step = strval((int)($step + 1));

            // Vérifier si l'étape suivante existe
            if (!isset($json_a->$realName->$next_step)) {
                echo "Conversation terminée";
                return;
            }

            echo $json_a->$realName->$next_step->{"message"};

            // Sauvegarder la progression dans la BDD si l'utilisateur est connecté
            if ($userId) {
                $this->saveProgress($userId, $name, (int)$next_step);
            }
        }
        // Sinon, renvoyer une réponse aléatoire
        else {
            $responses = $json_a->$realName->$step->{"responses"};
            echo $responses[array_rand($responses)];
        }
    }

    /**
     * Mappe les identifiants Instagram vers les vrais noms dans answers.json
     */
    private function mapUsernameToRealName($username): string
    {
        // Mapping des identifiants Instagram vers les vrais noms
        $mapping = [
            'mel_133' => 'Melina',
            'alex_photo' => 'Alexandre',
            'anna_food' => 'Sophie',
            'annie_nature' => 'Emma',
            'brooke_kitchen' => 'Julie',
            'christiann_bake' => 'Camille',
            'leo_creative' => 'Lucas',
            'diliara_style' => 'Marie',
            'corina_pets' => 'Thomas',
            'mike_coffee' => 'Antoine'
        ];

        // Si l'identifiant est dans le mapping, retourner le vrai nom
        // Sinon, retourner l'identifiant tel quel (peut-être que c'est déjà le bon nom)
        return $mapping[$username] ?? $username;
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