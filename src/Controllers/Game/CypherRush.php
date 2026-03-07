<?php
namespace Controllers\Game;

use Controllers\AbstractController;
use Models\Game\CypherGame;
use Views\Game\CypherRush\CypherRushView;
use Attributes\Route;

/**
 * Contrôleur du jeu CypherRush
 *
 * Mini-jeu éducatif de déchiffrement contre la montre.
 * Gère la génération de challenges, la vérification des réponses et le score.
 */
#[Route('/game/cypher-rush', name: 'game_cypher_rush')]
class CypherRush extends AbstractController
{
    /**
     * Affiche la page du jeu CypherRush
     * 
     * Redirige vers login si l'utilisateur n'est pas connecté.
     * 
     * @return void
     */
    function getMethod(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect(url('user_login'));
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
            redirect(url('user_login'));
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
                $this->jsonResponse(['success' => false, 'message' => 'Action invalide']);
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

        $this->jsonResponse([
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

            $explanation = $_SESSION['cypher_current_challenge']['explanation'] ?? '';

            // Réinitialiser le jeu avant jsonResponse (qui appelle exit)
            unset($_SESSION['cypher_game_started']);
            unset($_SESSION['cypher_current_challenge']);

            $this->jsonResponse([
                'success' => true,
                'correct' => true,
                'score' => $totalScore,
                'time' => $timeElapsed,
                'message' => '🎉 Bravo ! Message déchiffré avec succès !',
                'explanation' => $explanation
            ]);
        } else {
            $this->jsonResponse([
                'success' => true,
                'correct' => false,
                'message' => '❌ Ce n\'est pas la bonne réponse. Réessaie !'
            ]);
        }
    }

    private function getHint() {
        if (!isset($_SESSION['cypher_current_challenge'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Aucun jeu en cours']);
            return;
        }

        $hintsUsed = $_SESSION['cypher_hints_used'] ?? 0;
        $hints = $_SESSION['cypher_current_challenge']['hints'] ?? [];

        if ($hintsUsed < count($hints)) {
            $_SESSION['cypher_hints_used'] = $hintsUsed + 1;

            $this->jsonResponse([
                'success' => true,
                'hint' => $hints[$hintsUsed],
                'penalty' => 100,
                'hints_remaining' => count($hints) - $hintsUsed - 1
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Plus d\'indices disponibles !'
            ]);
        }
    }
}

