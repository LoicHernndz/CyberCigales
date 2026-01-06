<?php
namespace Controllers\Code\Hamming;

use Controllers\AbstractController;
use helpers\Code\Hamming as HammingHelper;
use Views\Code\Hamming\HammingToolView;

/**
 * Controleur pour l'outil Hamming (sans Ajax)
 * Page avec formulaire classique et rechargement de page
 */
class HammingTool extends AbstractController
{
    /**
     * Affiche un nouveau carre de Hamming avec une erreur
     */
    function getMethod()
    {
        // Generer un carre avec erreur
        $result = HammingHelper::generateSquareWithError();
        
        // Stocker dans la session pour verification
        $_SESSION['hamming_tool_square'] = $result['square'];
        $_SESSION['hamming_tool_original'] = $result['originalSquare'];
        $_SESSION['hamming_tool_error_pos'] = $result['errorPosition'];
        
        $data = [
            'square' => $result['square'],
            'message' => '',
            'messageType' => '',
            'showResult' => false
        ];
        
        $view = new HammingToolView($data);
        $view->render();
    }
    
    /**
     * Verifie la reponse de l'utilisateur (formulaire classique)
     */
    function postMethod()
    {
        $row = isset($_POST['row']) ? (int)$_POST['row'] : -1;
        $col = isset($_POST['col']) ? (int)$_POST['col'] : -1;
        
        // Verifier si on a les donnees de session
        if (!isset($_SESSION['hamming_tool_square']) || !isset($_SESSION['hamming_tool_original'])) {
            header('Location: /code/hamming');
            exit();
        }
        
        $squareWithError = $_SESSION['hamming_tool_square'];
        $originalSquare = $_SESSION['hamming_tool_original'];
        $errorPos = $_SESSION['hamming_tool_error_pos'];
        
        // Verifier si la reponse est correcte
        $isCorrect = ($row == $errorPos['row'] && $col == $errorPos['col']);
        
        if ($isCorrect) {
            $message = 'Bravo ! Vous avez trouve le bit errone en position [' . $row . ',' . $col . ']';
            $messageType = 'success';
            
            // Generer un nouveau carre pour le prochain essai
            $newResult = HammingHelper::generateSquareWithError();
            $_SESSION['hamming_tool_square'] = $newResult['square'];
            $_SESSION['hamming_tool_original'] = $newResult['originalSquare'];
            $_SESSION['hamming_tool_error_pos'] = $newResult['errorPosition'];
            $squareWithError = $newResult['square'];
        } else {
            $message = 'Erreur ! Le bit errone etait en position [' . $errorPos['row'] . ',' . $errorPos['col'] . ']. Reessayez avec ce nouveau carre.';
            $messageType = 'error';
            
            // Generer un nouveau carre
            $newResult = HammingHelper::generateSquareWithError();
            $_SESSION['hamming_tool_square'] = $newResult['square'];
            $_SESSION['hamming_tool_original'] = $newResult['originalSquare'];
            $_SESSION['hamming_tool_error_pos'] = $newResult['errorPosition'];
            $squareWithError = $newResult['square'];
        }
        
        $data = [
            'square' => $squareWithError,
            'message' => $message,
            'messageType' => $messageType,
            'showResult' => true,
            'wasCorrect' => $isCorrect,
            'correctRow' => $errorPos['row'],
            'correctCol' => $errorPos['col'],
            'clickedRow' => $row,
            'clickedCol' => $col
        ];
        
        $view = new HammingToolView($data);
        $view->render();
    }
}

