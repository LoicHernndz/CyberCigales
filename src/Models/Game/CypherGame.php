<?php
namespace Models\Game;

use config\Database;
use PDO;

class CypherGame
{
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Génère un défi de déchiffrement aléatoire
     */
    public function generateChallenge(): array {
        $challenges = [
            // Chiffrement César
            [
                'type' => 'cesar',
                'decrypted' => 'BUGZ EST UN PIRATE MASQUE',
                'key' => 3,
                'first_hint' => '🔍 Indice de Cyba : C\'est un chiffrement par décalage...',
                'hints' => [
                    '📝 Chaque lettre est décalée de la même valeur dans l\'alphabet',
                    '🔢 Le décalage est de 3 positions (A→D, B→E, C→F...)',
                    '💡 Jules César utilisait cette méthode pour ses messages secrets'
                ],
                'explanation' => 'Le chiffrement de César décale chaque lettre d\'un nombre fixe de positions dans l\'alphabet. Ici, un décalage de 3 : B→E, U→X, G→J, etc.'
            ],
            [
                'type' => 'cesar',
                'decrypted' => 'PROTEGEZ VOS DONNEES PERSONNELLES',
                'key' => 5,
                'first_hint' => '🔍 Indice de Cyba : Méthode antique de chiffrement...',
                'hints' => [
                    '📝 Décalage alphabétique constant',
                    '🔢 Le décalage est de 5 positions',
                    '💡 Essaye tous les décalages possibles (brute force)'
                ],
                'explanation' => 'Avec un décalage de 5, P→U, R→W, O→T. Le chiffrement César n\'a que 25 clés possibles, ce qui le rend vulnérable au cassage par force brute.'
            ],
            // Chiffrement Atbash (miroir)
            [
                'type' => 'atbash',
                'decrypted' => 'LA CYBERSECURITE EST IMPORTANTE',
                'key' => null,
                'first_hint' => '🔍 Indice de Cyba : Un miroir alphabétique...',
                'hints' => [
                    '📝 A↔Z, B↔Y, C↔X... l\'alphabet est inversé',
                    '🔢 Aucune clé nécessaire, c\'est un simple miroir',
                    '💡 Si tu chiffres deux fois, tu obtiens le message original'
                ],
                'explanation' => 'L\'Atbash inverse l\'alphabet : A devient Z, B devient Y, etc. C\'est un chiffrement par substitution monoalphabétique très ancien.'
            ],
            // Chiffrement inversé (reverse)
            [
                'type' => 'reverse',
                'decrypted' => 'NE CLIQUEZ JAMAIS SUR DES LIENS SUSPECTS',
                'key' => null,
                'first_hint' => '🔍 Indice de Cyba : Tout est à l\'envers...',
                'hints' => [
                    '📝 Lis le message de droite à gauche',
                    '🔢 Chaque mot est inversé individuellement',
                    '💡 C\'est le plus simple des chiffrements !'
                ],
                'explanation' => 'Le chiffrement par inversion lit simplement le texte à l\'envers. Très basique mais peut tromper un œil non averti.'
            ],
            // Chiffrement ROT13
            [
                'type' => 'rot13',
                'decrypted' => 'UTILISEZ UN MOT DE PASSE FORT',
                'key' => 13,
                'first_hint' => '🔍 Indice de Cyba : ROT quelque chose...',
                'hints' => [
                    '📝 C\'est un César avec un décalage spécial',
                    '🔢 Le décalage est exactement la moitié de l\'alphabet',
                    '💡 ROT13 : décalage de 13 positions'
                ],
                'explanation' => 'ROT13 est un cas spécial du chiffrement César avec un décalage de 13. Appliqué deux fois, il redonne le texte original (13+13=26).'
            ],
            // Messages liés au scénario
            [
                'type' => 'cesar',
                'decrypted' => 'LE RESEAU DES CIGALES A ETE COMPROMIS',
                'key' => 7,
                'first_hint' => '🔍 Indice de Cyba : Message intercepté de Bugz...',
                'hints' => [
                    '📝 Chiffrement par substitution alphabétique',
                    '🔢 Décalage de 7 positions',
                    '💡 C\'est un message crucial pour la mission !'
                ],
                'explanation' => 'Ce message prouve que Bugz a infiltré le système. Le décalage de 7 est plus difficile à deviner qu\'un simple ROT13.'
            ]
        ];
        
        // Sélectionner un défi aléatoire
        $challenge = $challenges[array_rand($challenges)];
        
        // Chiffrer le message
        $encrypted = $this->encrypt($challenge['decrypted'], $challenge['type'], $challenge['key']);
        
        return [
            'type' => $challenge['type'],
            'encrypted' => $encrypted,
            'decrypted' => $challenge['decrypted'],
            'key' => $challenge['key'],
            'first_hint' => $challenge['first_hint'],
            'hints' => $challenge['hints'],
            'explanation' => $challenge['explanation']
        ];
    }
    
    /**
     * Chiffre un message selon le type spécifié
     */
    private function encrypt(string $text, string $type, ?int $key): string {
        $text = strtoupper($text);
        
        switch($type) {
            case 'cesar':
            case 'rot13':
                return $this->caesarCipher($text, $key ?? 13);
                
            case 'atbash':
                return $this->atbashCipher($text);
                
            case 'reverse':
                return $this->reverseCipher($text);
                
            default:
                return $text;
        }
    }
    
    /**
     * Chiffrement de César
     */
    private function caesarCipher(string $text, int $shift): string {
        $result = '';
        $shift = $shift % 26; // Normaliser le décalage
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $shifted = (($ascii - 65 + $shift) % 26) + 65;
                $result .= chr($shifted);
            } else {
                $result .= $char; // Garder les espaces et caractères spéciaux
            }
        }
        
        return $result;
    }
    
    /**
     * Chiffrement Atbash (miroir alphabétique)
     */
    private function atbashCipher(string $text): string {
        $result = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $mirrored = 90 - ($ascii - 65); // Z(90) - distance depuis A(65)
                $result .= chr($mirrored);
            } else {
                $result .= $char;
            }
        }
        
        return $result;
    }
    
    /**
     * Chiffrement par inversion
     */
    private function reverseCipher(string $text): string {
        // Inverser chaque mot individuellement
        $words = explode(' ', $text);
        $reversed = array_map('strrev', $words);
        return implode(' ', $reversed);
    }
    
    /**
     * Sauvegarde le score d'un joueur
     */
    public function saveScore(int $userId, int $score, int $time, int $hints): bool {
        try {
            // Créer la table si elle n'existe pas
            $this->createTableIfNotExists();
            
            $query = "INSERT INTO cypher_scores (user_id, score, time_elapsed, hints_used, played_at) 
                      VALUES (:user_id, :score, :time, :hints, NOW())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
            $stmt->bindParam(':time', $time, PDO::PARAM_INT);
            $stmt->bindParam(':hints', $hints, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Erreur sauvegarde score Cypher Rush : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les meilleurs scores
     */
    public function getTopScores(int $limit = 10): array {
        try {
            $query = "SELECT cs.score, cs.time_elapsed, cs.hints_used, cs.played_at, u.username 
                      FROM cypher_scores cs 
                      INNER JOIN users u ON cs.user_id = u.id 
                      ORDER BY cs.score DESC, cs.time_elapsed ASC 
                      LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur récupération scores : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crée la table des scores si elle n'existe pas
     */
    private function createTableIfNotExists(): void {
        $query = "CREATE TABLE IF NOT EXISTS cypher_scores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            score INT NOT NULL,
            time_elapsed INT NOT NULL,
            hints_used INT NOT NULL,
            played_at DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_score (score DESC),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
    }
}


