<?php
/**
 * Contrôleur du mini-jeu Analyse Fréquentielle
 * 
 * Ce jeu permet à l'utilisateur de déchiffrer un message chiffré
 * par substitution monoalphabétique en utilisant ses connaissances
 * sur les fréquences des lettres en français.
 * 
 * AUCUN comptage automatique n'est fourni - l'utilisateur doit faire
 * sa propre recherche pour connaître les fréquences typiques.
 */
namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\FrequencyGame\FrequencyGameView;

class FrequencyGame extends AbstractController
{
    /**
     * Phrases à déchiffrer (variées en longueur et difficulté)
     * @var array<string>
     */
    private array $texts = [
        // Phrases courtes (faciles)
        "LA SECURITE COMMENCE PAR UN BON MOT DE PASSE",
        "NE JAMAIS PARTAGER SES IDENTIFIANTS",
        "TOUJOURS VERIFIER L'EXPEDITEUR D'UN EMAIL",

        // Phrases moyennes
        "LA CRYPTOGRAPHIE EST L'ART DE PROTEGER LES SECRETS",
        "UN HACKER PEUT EXPLOITER LA MOINDRE FAILLE",
        "LES MOTS DE PASSE DOIVENT ETRE UNIQUES ET COMPLEXES",
        "ATTENTION AUX LIENS SUSPECTS DANS LES EMAILS",

        // Phrases longues (difficiles)
        "L'ANALYSE FREQUENTIELLE PERMET DE CASSER UN CHIFFREMENT PAR SUBSTITUTION SIMPLE",
        "ALAN TURING A JOUE UN ROLE CRUCIAL DANS LE DECRYPTAGE DE LA MACHINE ENIGMA",
        "EN FRANCAIS LA LETTRE E EST LA PLUS FREQUENTE SUIVIE PAR LE A ET LE S",
        "LA DOUBLE AUTHENTIFICATION PROTEGE VOS COMPTES MEME SI VOTRE MOT DE PASSE EST VOLE"
    ];

    /**
     * Affiche la page du jeu
     * 
     * @return void
     */
    public function getMethod(): void
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        // Afficher la vue du jeu
        $view = new FrequencyGameView([
            'username' => $_SESSION['username'] ?? 'Analyste'
        ]);
        $view->render();
    }

    /**
     * Gère les actions POST du jeu
     * 
     * @return void
     */
    public function postMethod(): void
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        // Définir le header JSON
        header('Content-Type: application/json');

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'start_game':
                $this->startGame();
                break;
            case 'check_solution':
                $this->checkSolution();
                break;
            case 'get_hint':
                $this->getHint();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
    }

    /**
     * Démarre une nouvelle partie
     * 
     * Choisit un texte aléatoire et le chiffre avec une substitution
     * monoalphabétique aléatoire.
     * 
     * @return void
     */
    private function startGame(): void
    {
        // Choisir un texte aléatoire
        $originalText = $this->texts[array_rand($this->texts)];

        // Générer un alphabet de substitution aléatoire
        $alphabet = range('A', 'Z');
        $shuffled = $alphabet;
        shuffle($shuffled);
        $key = array_combine($alphabet, $shuffled);

        // Chiffrer le texte
        $encryptedText = '';
        foreach (str_split($originalText) as $char) {
            if (ctype_upper($char)) {
                $encryptedText .= $key[$char];
            } else {
                // Garder les espaces, apostrophes, etc.
                $encryptedText .= $char;
            }
        }

        // Stocker la solution en session
        $_SESSION['freq_game_solution'] = $originalText;
        $_SESSION['freq_game_encrypted'] = $encryptedText;
        $_SESSION['freq_game_key'] = $key;
        $_SESSION['freq_game_start'] = time();
        $_SESSION['freq_game_hints'] = 0;

        echo json_encode([
            'success' => true,
            'encrypted_text' => $encryptedText
        ]);
    }

    /**
     * Vérifie la solution proposée par l'utilisateur
     * 
     * @return void
     */
    private function checkSolution(): void
    {
        $userSolution = strtoupper(trim($_POST['solution'] ?? ''));
        $correctSolution = $_SESSION['freq_game_solution'] ?? '';

        // Nettoyer les solutions pour comparaison
        // (remplacer les underscores par des espaces et normaliser)
        $userSolution = str_replace('_', ' ', $userSolution);
        $userSolution = preg_replace('/\s+/', ' ', $userSolution);
        $correctSolution = preg_replace('/\s+/', ' ', $correctSolution);

        if ($userSolution === $correctSolution) {
            // Calculer le score
            $startTime = $_SESSION['freq_game_start'] ?? time();
            $elapsedSeconds = time() - $startTime;
            $hintsUsed = $_SESSION['freq_game_hints'] ?? 0;

            $baseScore = 1000;
            $timeBonus = max(0, 500 - ($elapsedSeconds * 5));
            $hintPenalty = $hintsUsed * 100;
            $finalScore = max(0, $baseScore + $timeBonus - $hintPenalty);

            echo json_encode([
                'success' => true,
                'message' => 'Bravo ! Tu as décrypté le message !',
                'correct' => true,
                'score' => $finalScore,
                'time' => $elapsedSeconds,
                'hints' => $hintsUsed
            ]);
        } else {
            // Compter le nombre de lettres correctes pour feedback
            $correct = 0;
            $total = 0;
            $solChars = str_split($correctSolution);
            $userChars = str_split($userSolution);

            for ($i = 0; $i < min(count($solChars), count($userChars)); $i++) {
                if (ctype_alpha($solChars[$i])) {
                    $total++;
                    if (isset($userChars[$i]) && $solChars[$i] === $userChars[$i]) {
                        $correct++;
                    }
                }
            }

            $percentage = $total > 0 ? round(($correct / $total) * 100) : 0;

            echo json_encode([
                'success' => true,
                'message' => "Pas encore... Tu as environ {$percentage}% de bonnes lettres. Continue !",
                'correct' => false,
                'percentage' => $percentage
            ]);
        }
    }

    /**
     * Donne un indice à l'utilisateur
     * 
     * @return void
     */
    private function getHint(): void
    {
        $hintsUsed = $_SESSION['freq_game_hints'] ?? 0;

        if ($hintsUsed >= 3) {
            echo json_encode([
                'success' => false,
                'message' => 'Tu as utilisé tous tes indices !'
            ]);
            return;
        }

        $_SESSION['freq_game_hints'] = $hintsUsed + 1;

        echo json_encode([
            'success' => true,
            'hint_number' => $hintsUsed + 1
        ]);
    }
}
