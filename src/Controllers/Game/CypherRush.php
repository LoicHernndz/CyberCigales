<?php
namespace Controllers\Game;

use Controllers\AbstractController;
use Models\Game\CypherGame;
use Views\Game\CypherRush\CypherRushView;

class CypherRush extends AbstractController
{
    // Méthode principale appelée lorsque la route correspond à ce contrôleur
    function getMethod(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        // Données à passer à la vue
        $data = [
            'username' => $_SESSION['username'] ?? 'Analyste',
            'game_started' => $_SESSION['cypher_game_started'] ?? false,
            'score' => $_SESSION['cypher_score'] ?? 0
        ];
        
        // Création d'une instance de la vue "CypherRushView"
        $view = new CypherRushView($data);
        
        // Affichage de la page du jeu
        $view->render();
    }

    function postMethod()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $action = $_POST['action'] ?? '';

        switch($action) {
            case 'start_game':
                $this->startGame();
                break;

            case 'submit_answer':
                $this->submitAnswer();
                break;

            case 'get_hint':
                $this->getHint();
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
    }

    private function startGame() {
        // Générer un nouveau message chiffré
        $cypherGame = new CypherGame();
        $gameData = $cypherGame->generateChallenge();

        // Stocker dans la session
        $_SESSION['cypher_game_started'] = true;
        $_SESSION['cypher_current_challenge'] = $gameData;
        $_SESSION['cypher_start_time'] = time();
        $_SESSION['cypher_hints_used'] = 0;

        echo json_encode([
            'success' => true,
            'encrypted_message' => $gameData['encrypted'],
            'cipher_type' => $gameData['type'],
            'hint' => $gameData['first_hint']
        ]);
    }

    private function submitAnswer() {
        $userAnswer = trim($_POST['answer'] ?? '');
        $correctAnswer = $_SESSION['cypher_current_challenge']['decrypted'] ?? '';

        // Calculer le temps écoulé
        $timeElapsed = time() - ($_SESSION['cypher_start_time'] ?? time());
        $hintsUsed = $_SESSION['cypher_hints_used'] ?? 0;

        // Vérifier la réponse (insensible à la casse et espaces)
        $isCorrect = strtolower($userAnswer) === strtolower($correctAnswer);

        if ($isCorrect) {
            // Calculer le score
            $baseScore = 1000;
            $timeBonus = max(0, 500 - ($timeElapsed * 5)); // -5 points par seconde
            $hintPenalty = $hintsUsed * 100; // -100 points par indice
            $totalScore = max(100, $baseScore + $timeBonus - $hintPenalty);

            // Sauvegarder le score
            $cypherGame = new CypherGame();
            $cypherGame->saveScore($_SESSION['user_id'], $totalScore, $timeElapsed, $hintsUsed);

            $_SESSION['cypher_score'] = $totalScore;

            echo json_encode([
                'success' => true,
                'correct' => true,
                'score' => $totalScore,
                'time' => $timeElapsed,
                'message' => '🎉 Bravo ! Message déchiffré avec succès !',
                'explanation' => $_SESSION['cypher_current_challenge']['explanation'] ?? ''
            ]);

            // Réinitialiser le jeu
            unset($_SESSION['cypher_game_started']);
            unset($_SESSION['cypher_current_challenge']);
        } else {
            echo json_encode([
                'success' => true,
                'correct' => false,
                'message' => '❌ Ce n\'est pas la bonne réponse. Réessaie !'
            ]);
        }
    }

    private function getHint() {
        if (!isset($_SESSION['cypher_current_challenge'])) {
            echo json_encode(['success' => false, 'message' => 'Aucun jeu en cours']);
            return;
        }

        $hintsUsed = $_SESSION['cypher_hints_used'] ?? 0;
        $hints = $_SESSION['cypher_current_challenge']['hints'] ?? [];

        if ($hintsUsed < count($hints)) {
            $_SESSION['cypher_hints_used'] = $hintsUsed + 1;

            echo json_encode([
                'success' => true,
                'hint' => $hints[$hintsUsed],
                'penalty' => 100,
                'hints_remaining' => count($hints) - $hintsUsed - 1
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Plus d\'indices disponibles !'
            ]);
        }
    }
}

