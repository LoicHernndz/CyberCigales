<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use helpers\Code\Hamming as HammingHelper;
use Views\Code\Hamming\HammingView;

class Hamming extends AbstractController
{
    /**
     * Affiche la page de déverrouillage.
     *
     * @return void
     */
    function getMethod()
    {

        $_SESSION['hamming_streak'] = 0;

        $view = new HammingView([]);
        $view->render();
    }

    /**
     * Gère soit le déverrouillage du jeu, soit les clics AJAX sur les cellules.
     *
     * @return void
     */
    function postMethod()
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            $row = isset($_POST['row']) ? (int)$_POST['row'] : -1;
            $col = isset($_POST['col']) ? (int)$_POST['col'] : -1;

            if (
                !isset($_SESSION['hamming_square']) ||
                !isset($_SESSION['hamming_original']) ||
                !isset($_SESSION['hamming_error_pos'])
            ) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => 0,
                    'message' => 'Session expirée'
                ]);
                exit();
            }

            $squareWithError = $_SESSION['hamming_square'];
            $errorPos = $_SESSION['hamming_error_pos'];

            if (!isset($_SESSION['hamming_streak'])) {
                $_SESSION['hamming_streak'] = 0;
            }

            $isCorrect = ($row === $errorPos['row'] && $col === $errorPos['col']);
            $resultValue = $isCorrect ? 1 : 0;

            if ($isCorrect) {
                $_SESSION['hamming_streak'] = ($_SESSION['hamming_streak'] ?? 0) + 1;

                if ($_SESSION['hamming_streak'] >= 5) {
                    $_SESSION['hamming_streak'] = 0;
                }

                $newResult = HammingHelper::generateSquareWithError();
                $_SESSION['hamming_square'] = $newResult['square'];
                $_SESSION['hamming_original'] = $newResult['originalSquare'];
                $_SESSION['hamming_error_pos'] = $newResult['errorPosition'];
                $squareWithError = $newResult['square'];
            } else {
                $_SESSION['hamming_streak'] = 0;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $resultValue,
                'result' => $resultValue,
                'square' => $squareWithError,
                'newSquare' => $isCorrect,
                'streak' => $_SESSION['hamming_streak'] ?? 0,
                'target' => 5
            ]);
            exit();
        }

        $message = trim((string)($_POST['message'] ?? ''));

        if ($message !== 'access granted') {
            flash('hamming', 'Impossible de traiter ce message...');
            header('Location: /code/hamming');
            exit();
        }

        $result = HammingHelper::generateSquareWithError();

        $_SESSION['hamming_square'] = $result['square'];
        $_SESSION['hamming_original'] = $result['originalSquare'];
        $_SESSION['hamming_error_pos'] = $result['errorPosition'];

        if (!isset($_SESSION['hamming_streak'])) {
            $_SESSION['hamming_streak'] = 0;
        }

        $view = new HammingView([
            'square' => $result['square'],
            'streak' => $_SESSION['hamming_streak'],
            'target' => 5
        ]);
        $view->render();
    }
}