<?php
namespace Models\Instagram;

use config\Database;

/**
 * Modèle pour les données Instagram
 * 
 * Ce modèle gère toutes les données statiques et dynamiques
 * pour les pages Instagram (stories, posts, profils, etc.)
 */
class InstagramModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->createMessagesTableIfNotExists();
    }
    /**
     * Récupère les données des stories
     * 
     * @return array Tableau des stories avec leurs informations
     */
    public function getStories(): array
    {
        return [
            [
                'username' => 'mel_133',
                'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
                'is_yours' => false,
                'profile_url' => '/instagram/melina',
                'is_unseen' => true
            ],
            [
                'username' => 'alex_photo',
                'avatar' => 'images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'anna_food',
                'avatar' => 'images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'annie_nature',
                'avatar' => 'images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'brooke_kitchen',
                'avatar' => 'images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'christiann_bake',
                'avatar' => 'images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg',
                'is_yours' => false
            ]
        ];
    }
    
    /**
     * Récupère les données des posts du feed
     * 
     * @return array Tableau des posts avec leurs informations
     */
    public function getPosts(): array
    {
        return [
            [
                'id' => 1,
                'username' => 'alex_photo',
                'avatar' => 'images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'location' => 'Paris, France',
                'image' => 'images/instagram/daniel-lincoln-IE2Z11zKsso-unsplash.jpg',
                'likes' => 1234,
                'caption' => 'Magnifique journée à Paris ! 🌟 #paris #photographie #coucherdesoleil',
                'comments' => [
                    [
                        'username' => 'marie_photo',
                        'text' => 'Tellement beau ! 😍'
                    ],
                    [
                        'username' => 'pierre_art',
                        'text' => 'Composition incroyable !'
                    ]
                ],
                'time' => 'Il y a 2 heures'
            ],
            [
                'id' => 2,
                'username' => 'anna_food',
                'avatar' => 'images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'location' => 'Restaurant Le Petit',
                'image' => 'images/instagram/nathan-dumlao-nBJHO6wmRWw-unsplash.jpg',
                'likes' => 892,
                'caption' => 'Le café parfait pour commencer la journée ☕️ #café #matin #modevie',
                'comments' => [
                    [
                        'username' => 'coffee_lover',
                        'text' => 'J\'ai envie d\'un café maintenant !'
                    ]
                ],
                'time' => 'Il y a 4 heures'
            ],
            [
                'id' => 3,
                'username' => 'annie_nature',
                'avatar' => 'images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'location' => 'Jardin Botanique',
                'image' => 'images/instagram/annie-spratt-eZk9w9RBHRo-unsplash.jpg',
                'likes' => 2156,
                'caption' => 'Moment de paix dans la nature 🌿 #nature #paix #vert #botanique',
                'comments' => [
                    [
                        'username' => 'nature_lover',
                        'text' => 'Tellement apaisant !'
                    ],
                    [
                        'username' => 'green_thumb',
                        'text' => 'J\'adore cette ambiance !'
                    ]
                ],
                'time' => 'Il y a 6 heures'
            ]
        ];
    }
    
    /**
     * Récupère les données du profil de Melina
     * 
     * @return array Données du profil
     */
    public function getMelinaProfile(): array
    {
        return [
            'username' => 'mel_133',
            'display_name' => 'Melina',
            'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
            'posts_count' => '3',
            'followers_count' => '89.2K',
            'following_count' => '1,156',
            'bio' => '✨ Fashion & Lifestyle ✨\n📸 Photographer\n🎨 Creative soul\n📍 Paris, France\n MARSEILLE 13\n\n#fashion #lifestyle #photography #paris #creative',
            'website' => 'cybercigales.fr',
            'verified' => true
        ];
    }
    
    /**
     * Récupère les posts du profil de Melina
     * 
     * @return array Posts du profil
     */
    public function getMelinaPosts(): array
    {
        return [
            [
                'id' => 1,
                'image' => '/images/instagram/tyler-delgado-A1kXxn2KVCM-unsplash 2.jpg',
                'type' => 'pinned',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ],
            [
                'id' => 2,
                'image' => '/images/instagram/steve-doig-FaMBWkmvPyY-unsplash.jpg',
                'type' => 'normal',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ],
            [
                'id' => 3,
                'image' => '/images/instagram/faux-profil-amie-hacke/melina_photo_miroir_2.png',
                'type' => 'normal',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ]
        ];
    }
    
    /**
     * Récupère les messages du chat avec Melina depuis la base de données pour la session actuelle
     * 
     * @param string $sessionId ID de la session PHP
     * @return array Messages du chat
     */
    /**
     * Récupère les messages du chat avec Melina depuis la base de données
     * 
     * @param string $sessionId ID de la conversation (conversationId généré par JavaScript)
     * @return array Messages du chat triés par date de création
     */
    public function getMelinaChatMessages(string $sessionId = ''): array
    {
        try {
            // Si pas d'ID de conversation, retourner les messages par défaut
            if (empty($sessionId)) {
                return $this->getDefaultMessages();
            }
            
            // Récupérer les messages de cette conversation uniquement, triés par date
            $this->db->query('SELECT type, content, created_at FROM instagram_messages WHERE session_id = :session_id ORDER BY created_at ASC');
            $this->db->bind(':session_id', $sessionId);
            $results = $this->db->resultSet();
            
            $messages = [];
            foreach ($results as $row) {
                $messages[] = [
                    'type' => $row->type,
                    'content' => $row->content,
                    'time' => date('H:i', strtotime($row->created_at))
                ];
            }
            
            // Si aucun message pour cette conversation, initialiser avec les messages par défaut
            if (empty($messages)) {
                $this->initializeDefaultMessages($sessionId);
                return $this->getMelinaChatMessages($sessionId);
            }
            
            return $messages;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner les messages par défaut
            return $this->getDefaultMessages();
        }
    }
    
    /**
     * Sauvegarde un nouveau message dans la base de données pour la session actuelle
     * 
     * @param string $type Type de message ('sent' ou 'received')
     * @param string $content Contenu du message
     * @param string $sessionId ID de la session PHP
     * @return bool True si la sauvegarde a réussi, false sinon
     */
    /**
     * Sauvegarde un nouveau message dans la base de données
     * 
     * @param string $type Type de message ('sent' ou 'received')
     * @param string $content Contenu du message
     * @param string $sessionId ID de la conversation (conversationId généré par JavaScript)
     * @return bool True si la sauvegarde a réussi, false sinon
     */
    public function saveMessage(string $type, string $content, string $sessionId = ''): bool
    {
        try {
            // Vérifier que l'ID de conversation est fourni (obligatoire)
            if (empty($sessionId)) {
                return false;
            }
            
            // S'assurer que la table existe
            $this->createMessagesTableIfNotExists();
            
            // Insérer le message dans la base de données
            $this->db->query('INSERT INTO instagram_messages (session_id, type, content, created_at) VALUES (:session_id, :type, :content, NOW())');
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':type', $type);
            $this->db->bind(':content', $content);
            
            return $this->db->execute();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Crée la table des messages si elle n'existe pas
     */
    private function createMessagesTableIfNotExists(): void
    {
        try {
            // D'abord créer la table de base
            $query = "CREATE TABLE IF NOT EXISTS instagram_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL DEFAULT '',
                type ENUM('sent', 'received') NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME NOT NULL,
                INDEX idx_session_id (session_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->db->exec($query);
            
            // Vérifier si la colonne session_id existe en interrogeant INFORMATION_SCHEMA
            try {
                $checkQuery = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'instagram_messages' 
                              AND COLUMN_NAME = 'session_id'";
                $this->db->query($checkQuery);
                $result = $this->db->single();
                
                // Si la colonne n'existe pas (count = 0), l'ajouter
                if ($result && $result->count == 0) {
                    $alterQuery = "ALTER TABLE instagram_messages ADD COLUMN session_id VARCHAR(255) NOT NULL DEFAULT '' AFTER id";
                    $this->db->exec($alterQuery);
                    
                    $indexQuery = "ALTER TABLE instagram_messages ADD INDEX idx_session_id (session_id)";
                    $this->db->exec($indexQuery);
                }
            } catch (\Exception $e) {
                try {
                    $alterQuery = "ALTER TABLE instagram_messages ADD COLUMN session_id VARCHAR(255) NOT NULL DEFAULT '' AFTER id";
                    $this->db->exec($alterQuery);
                    $indexQuery = "ALTER TABLE instagram_messages ADD INDEX idx_session_id (session_id)";
                    $this->db->exec($indexQuery);
                } catch (\Exception $e2) {
                    // La colonne existe déjà
                }
            }
        } catch (\Exception $e) {
            // Table existe déjà
        }
    }
    
    /**
     * Initialise les messages par défaut dans la base de données pour une session
     * 
     * @param string $sessionId ID de la session PHP
     */
    private function initializeDefaultMessages(string $sessionId): void
    {
        $defaultMessages = [
            ['type' => 'received', 'content' => 'Salut ! Comment ça va ? 😊'],
            ['type' => 'sent', 'content' => 'Salut Melina ! Ça va super, merci !'],
            ['type' => 'received', 'content' => 'J\'ai vu tes nouvelles photos, elles sont magnifiques ! 📸'],
            ['type' => 'sent', 'content' => 'Merci beaucoup ! J\'adore la photographie ✨']
        ];
        
        foreach ($defaultMessages as $message) {
            $this->saveMessage($message['type'], $message['content'], $sessionId);
        }
    }
    
    /**
     * Retourne les messages par défaut (fallback en cas d'erreur)
     * 
     * @return array Messages par défaut
     */
    public function getDefaultMessages(): array
    {
        return [
            [
                'type' => 'received',
                'content' => 'Salut ! Comment ça va ? 😊',
                'time' => '14:30'
            ],
            [
                'type' => 'sent',
                'content' => 'Salut Melina ! Ça va super, merci !',
                'time' => '14:32'
            ],
            [
                'type' => 'received',
                'content' => 'J\'ai vu tes nouvelles photos, elles sont magnifiques ! 📸',
                'time' => '14:35'
            ],
            [
                'type' => 'sent',
                'content' => 'Merci beaucoup ! J\'adore la photographie ✨',
                'time' => '14:37'
            ]
        ];
    }
    
}
