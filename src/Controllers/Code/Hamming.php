<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use helpers\Code\Hamming as HammingHelper;
use Views\Code\Hamming\HammingView;

class Hamming extends AbstractController
{

    private const key = "MON NOM EST ALEXANDRE SCHMIDT. TOUT EST CHIFFRÉ. J'AI UNE CLE A ENVOYER MAIS JE DOIS PAYER POUR LA DECRYPTER : WDSKAVZSJNBCS";
    private const noise = "AFO QMP EIJ YZDKSLCFR AIZDKSQ. ZEFJ ZEI ADZOKFE. A'AI JIE VBC X FQMLKFP EIOS BA MWLK KFEZH ASXN LP ADZSDOICO : FEOIKSDQPHEAZ";

    /**
     * Affiche la page de déverrouillage.
     *
     * @return void
     */
    function getMethod()
    {

        $_SESSION['hamming_progress'] = 0;

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

            if (!isset($_SESSION['hamming_progress'])) {
                $_SESSION['hamming_progress'] = 0;
            }

            $isCorrect = ($row === $errorPos['row'] && $col === $errorPos['col']);
            $resultValue = $isCorrect ? 1 : 0;
            $target = str_word_count(self::key);
            $hasCompleted = false;

            if ($isCorrect) {
                if ($_SESSION['hamming_progress'] < $target) {
                    $_SESSION['hamming_progress'] = ($_SESSION['hamming_progress'] ?? 0) + 1;
                }

                $hasCompleted = $_SESSION['hamming_progress'] >= $target;

                // Ne plus générer de nouveau carré une fois le message complet révélé
                if (!$hasCompleted) {
                    $newResult = HammingHelper::generateSquareWithError();
                    $_SESSION['hamming_square'] = $newResult['square'];
                    $_SESSION['hamming_original'] = $newResult['originalSquare'];
                    $_SESSION['hamming_error_pos'] = $newResult['errorPosition'];
                    $squareWithError = $newResult['square'];
                }
            } else {
                if ($_SESSION['hamming_progress'] > 0) {
                    $_SESSION['hamming_progress'] = ($_SESSION['hamming_progress'] ?? 0) - 1;
                }
            }

            // Ajout d'autant de mots du message que de nombres de succès, puis remplissage par du bruit.
            $message = implode(" ", array_slice(explode(" ", self::key), 0, $_SESSION['hamming_progress']));
            $message .= " " . implode(" ", array_slice(explode(" ", self::noise), $_SESSION['hamming_progress']));

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $resultValue,
                'result' => $resultValue,
                'message' => $message,
                'square' => $squareWithError,
                'newSquare' => $isCorrect && !$hasCompleted,
                'progress' => $_SESSION['hamming_progress'] ?? 0,
                'target' => $target,
                'complete' => $hasCompleted,
            ]);
            exit();
        }

        $message = trim((string)($_POST['message'] ?? ''));

        if ($message !== 'access granted' && !str_contains($message, self::noise)) {
            flash('hamming', 'Impossible de traiter ce message...');
            header('Location: /code/hamming');
            exit();
        }

        $result = HammingHelper::generateSquareWithError();

        $_SESSION['hamming_square'] = $result['square'];
        $_SESSION['hamming_original'] = $result['originalSquare'];
        $_SESSION['hamming_error_pos'] = $result['errorPosition'];

        if (!isset($_SESSION['hamming_progress'])) {
            $_SESSION['hamming_progress'] = 0;
        }

        // Préparer le message de jeu initial (tout le bruit, aucun mot corrigé)
        $target = str_word_count(self::key);
        $progress = $_SESSION['hamming_progress'] ?? 0;
        $initialMessage = implode(" ", array_slice(explode(" ", self::key), 0, $progress));
        $initialMessage .= " " . implode(" ", array_slice(explode(" ", self::noise), $progress));

        $view = new HammingView([
            'square' => $result['square'],
            'message' => $message,
            'progress' => $progress,
            'target' => $target,
            'renderedMessage' => trim($initialMessage),
        ]);
        $view->render();
    }
}