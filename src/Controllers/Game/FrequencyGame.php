<?php
namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\FrequencyGame\FrequencyGameView;

/**
 * Contrôleur du jeu d'analyse fréquentielle
 * 
 * Jeu éducatif de cryptanalyse par analyse de fréquences.
 * Génère un texte chiffré par substitution et challenge l'utilisateur à le déchiffrer.
 */
class FrequencyGame extends AbstractController
{
    private array $texts = [
        "LA CRYPTOGRAPHIE EST L'ART DU SECRET. ELLE PROTEGE VOS DONNEES CONTRE LES YEUX INDISCRETS.",
        "UNE ANALYSE FREQUENTIELLE PERMET DE CASSER UN CHIFFREMENT PAR SUBSTITUTION SIMPLE EN COMPTANT LES LETTRES.",
        "EN FRANCAIS LA LETTRE E EST LA PLUS FREQUENTE SUIVIE PAR LE A ET LE S.",
        "ALAN TURING A JOUE UN ROLE CRUCIAL DANS LE DECRYPTAGE DE LA MACHINE ENIGMA PENDANT LA GUERRE.",
        "LE CHIFFREMENT DE CESAR EST L'UN DES PLUS ANCIENS ET DES PLUS SIMPLES SYSTEMES DE CHIFFREMENT."
    ];

    /**
     * Affiche la page du jeu d'analyse fréquentielle
     * 
     * Redirige vers login si l'utilisateur n'est pas connecté.
     * 
     * @return void
     */
    function getMethod(){
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        $view = new FrequencyGameView([
            'username' => $_SESSION['username'] ?? 'Analyste'
        ]);
        $view->render();
    }

    function postMethod()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        $action = $_POST['action'] ?? '';

        switch($action) {
            case 'start_game':
                $this->startGame();
                break;
            case 'check_solution':
                $this->checkSolution();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
    }

    private function startGame() {
        // Pick a random text
        $originalText = $this->texts[array_rand($this->texts)];
        
        // Generate a random substitution alphabet
        $alphabet = range('A', 'Z');
        $shuffled = $alphabet;
        shuffle($shuffled);
        $key = array_combine($alphabet, $shuffled);
        
        // Encrypt the text
        $encryptedText = '';
        foreach (str_split($originalText) as $char) {
            if (ctype_upper($char)) {
                $encryptedText .= $key[$char];
            } else {
                $encryptedText .= $char;
            }
        }

        // Store solution in session
        $_SESSION['freq_game_solution'] = $originalText;
        $_SESSION['freq_game_start'] = time();

        echo json_encode([
            'success' => true,
            'encrypted_text' => $encryptedText
        ]);
    }

    private function checkSolution() {
        $userSolution = strtoupper(trim($_POST['solution'] ?? ''));
        $correctSolution = $_SESSION['freq_game_solution'] ?? '';

        if ($userSolution === $correctSolution) {
             echo json_encode([
                'success' => true,
                'message' => 'Bravo ! Vous avez décrypté le message.',
                'correct' => true
            ]);
        } else {
             echo json_encode([
                'success' => true,
                'message' => 'Ce n\'est pas tout à fait ça. Continuez vos efforts !',
                'correct' => false
            ]);
        }
    }
}
