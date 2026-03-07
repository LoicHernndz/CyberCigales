<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\FrequencyGame\FrequencyGameView;
use Attributes\Route;

/**
 * Contrôleur du jeu d'analyse fréquentielle
 *
 * Mini-jeu éducatif où l'utilisateur doit décrypter un texte
 * chiffré par substitution simple en analysant la fréquence des lettres.
 */
#[Route('/game/frequency', name: 'game_frequency')]
class Frequency extends AbstractController
{
    /**
     * Textes originaux utilisés pour générer les challenges
     *
     * @var array<int, string>
     */
    private array $texts = [
        "LE MEILLEUR SITE SELON MOI EST LE SITE INTERNET WWW.LEMONDE.FR.",
        "VA VOIR LE SITE INTERNET WWW.LEMONDE.FR POUR APPRENDRE PLEIN DE BELLES CHOSES.",
        "POUR EN APPRENDRE D'AVANTAGE, REGARDE LE SITE INTERNET WWW.LEMONDE.FR.",
        "ON APPREND PLEIN DE CHOSES SUR LE SITE INTERNET WWW.LEMONDE.FR.",
        "WWW.LEMONDE.FR, EST UN SITE SUR LEQUEL ON APPREND PLEIN DE CHOSE.",
    ];

    /**
     * Affiche la page du jeu d'analyse fréquentielle
     *
     * Redirige vers login si l'utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('user_login'));
        }
        $view = new FrequencyGameView([
            'username' => $_SESSION['username'] ?? 'Analyste'
        ]);
        $view->render();
    }

    /**
     * Traite les actions du jeu (démarrer, vérifier la solution)
     *
     * Redirige vers login si l'utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function postMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('user_login'));
        }
        $action = $_POST['action'] ?? '';

        $code = $_POST['code'] ?? '';
        switch ($action) {
            case 'verify_code':
                if ($code == '8265') {
                    $this->startGoodCodePage();
                } else {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Code incorrect'
                    ]);
                }
                break;
            case 'start_game':
                $this->startGame();
                break;
            case 'check_solution':
                $this->checkSolution();
                break;
            default:
                $this->jsonResponse(['success' => false, 'message' => 'Action invalide']);
        }
    }

    private function startGoodCodePage(): void {
        $this->jsonResponse([
            'success' => true,
            'message' => 'Code correct'
        ]);
    }

    /**
     * Génère un nouveau texte chiffré par substitution aléatoire
     *
     * Stocke la solution en session et retourne le texte chiffré en JSON.
     *
     * @return void
     */
    private function startGame(): void
    {
        $originalText = $this->texts[array_rand($this->texts)];
        $alphabet = range('A', 'Z');
        $shuffled = $alphabet;
        shuffle($shuffled);
        $key = array_combine($alphabet, $shuffled);
        $encryptedText = '';
        foreach (str_split($originalText) as $char) {
            if (ctype_upper($char)) {
                $encryptedText .= $key[$char];
            } else {
                $encryptedText .= $char;
            }
        }
        $_SESSION['freq_game_solution'] = $originalText;
        $_SESSION['freq_game_start'] = time();
        $this->jsonResponse(['success' => true, 'encrypted_text' => $encryptedText]);
    }

    /**
     * Vérifie la solution soumise par l'utilisateur
     *
     * Compare la réponse (insensible à la casse) avec la solution en session.
     *
     * @return void
     */
    private function checkSolution(): void
    {
        $userSolution = strtoupper(trim($_POST['solution'] ?? ''));
        $correctSolution = $_SESSION['freq_game_solution'] ?? '';
        if ($userSolution === $correctSolution) {
            $this->jsonResponse(['success' => true, 'message' => 'Bravo ! Vous avez décrypté le message.', 'correct' => true]);
        } else {
            $this->jsonResponse(['success' => true, 'message' => 'Ce n\'est pas tout à fait ça. Continuez vos efforts !', 'correct' => false]);
        }
    }
}
