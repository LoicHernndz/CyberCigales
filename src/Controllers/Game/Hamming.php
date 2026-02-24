<?php
namespace Controllers\Game;

use Controllers\AbstractController;
use helpers\Code\Hamming as HammingHelper;
use Views\Game\Hamming\HammingView;

/**
 * Contrôleur pour le mini-jeu du carré de Hamming
 * 
 * Gère l'affichage du carré avec erreur et la vérification des réponses
 * des utilisateurs via requêtes GET (affichage) et POST (vérification)
 */
class Hamming extends AbstractController
{
    /**
     * Affiche un nouveau carré de Hamming avec une erreur
     * 
     * Génère un carré aléatoire, introduit une erreur, et stocke
     * les données dans la session pour vérification ultérieure.
     * Initialise le système de progression si nécessaire.
     * 
     * @return void
     */
    function getMethod()
    {
        // Initialiser le système de progression si nécessaire
        if (!isset($_SESSION['hamming_streak'])) {
            $_SESSION['hamming_streak'] = 0;
        }
        
        // Générer un carré avec erreur
        $result = HammingHelper::generateSquareWithError();
        
        // Stocker dans la session pour vérification
        $_SESSION['hamming_square'] = $result['square'];
        $_SESSION['hamming_original'] = $result['originalSquare'];
        $_SESSION['hamming_error_pos'] = $result['errorPosition'];
        
        // Données à passer à la vue
        $data = [
            'square' => $result['square'],
            'success' => null, // Pas encore de résultat
            'message' => '',
            'streak' => $_SESSION['hamming_streak'] ?? 0,
            'target' => 5
        ];
        
        $view = new HammingView($data);
        $view->render();
    }
    
    /**
     * Vérifie la réponse de l'utilisateur et retourne le résultat
     * 
     * Reçoit la position cliquée (row, col) via POST et vérifie
     * si elle correspond à la position de l'erreur stockée en session.
     * Retourne JSON en cas de requête AJAX, sinon affiche la page.
     * 
     * @return void
     */
    function postMethod()
    {
        // Récupérer les données POST
        $row = isset($_POST['row']) ? (int)$_POST['row'] : -1;
        $col = isset($_POST['col']) ? (int)$_POST['col'] : -1;
        
        // Vérifier si on a les données de session
        if (!isset($_SESSION['hamming_square']) || !isset($_SESSION['hamming_original'])) {
            // Si requête AJAX, retourner JSON
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Session expirée', 'redirect' => '/game/hamming']);
                exit();
            }
            // Sinon rediriger vers GET
            header('Location: /game/hamming');
            exit();
        }
        
        $squareWithError = $_SESSION['hamming_square'];
        $errorPos = $_SESSION['hamming_error_pos'];
        
        // Initialiser le système de progression si nécessaire
        if (!isset($_SESSION['hamming_streak'])) {
            $_SESSION['hamming_streak'] = 0;
        }
        
        // Vérifier si la réponse est correcte
        $isCorrect = ($row == $errorPos['row'] && $col == $errorPos['col']);
        $resultValue = $isCorrect ? 1 : 0;
        
        // Gérer le système de progression
        if ($isCorrect) {
            // Incrémenter la série de victoires
            $_SESSION['hamming_streak'] = ($_SESSION['hamming_streak'] ?? 0) + 1;
            
            // Vérifier si l'objectif est atteint (5 victoires)
            if ($_SESSION['hamming_streak'] >= 5) {
                $_SESSION['hamming_streak'] = 0; // Réinitialiser la série
                $seriesComplete = true;
            } else {
                $seriesComplete = false;
            }
            
            // Générer un nouveau carré pour le prochain essai
            $newResult = HammingHelper::generateSquareWithError();
            $_SESSION['hamming_square'] = $newResult['square'];
            $_SESSION['hamming_original'] = $newResult['originalSquare'];
            $_SESSION['hamming_error_pos'] = $newResult['errorPosition'];
            $squareWithError = $newResult['square'];
        } else {
            // Erreur : réinitialiser la série
            $_SESSION['hamming_streak'] = 0;
            $seriesComplete = false;
        }
        
        // Préparer le message selon le résultat
        if ($isCorrect) {
            if ($seriesComplete) {
                $message = 'Bravo ! Série de 5 complétée. Nouvelle série commencée.';
            } else {
                $message = 'Bravo ! Série : ' . $_SESSION['hamming_streak'] . '/5';
            }
        } else {
            $message = 'Erreur ! La série est réinitialisée.';
        }
        
        // Si requête AJAX, retourner JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $resultValue,
                'result' => $resultValue, // 1 ou 0
                'message' => $message,
                'square' => $squareWithError,
                'newSquare' => $isCorrect,
                'streak' => $_SESSION['hamming_streak'] ?? 0,
                'target' => 5
            ]);
            exit();
        }
        
        // Sinon, afficher la page normalement (fallback)
        $data = [
            'square' => $squareWithError,
            'success' => $resultValue,
            'message' => $message,
            'streak' => $_SESSION['hamming_streak'] ?? 0,
            'target' => 5,
            'clickedRow' => $row,
            'clickedCol' => $col,
            'correctRow' => $errorPos['row'],
            'correctCol' => $errorPos['col']
        ];
        
        $view = new HammingView($data);
        $view->render();
    }
}

