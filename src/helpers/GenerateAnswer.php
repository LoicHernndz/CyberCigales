<?php

namespace helpers;

use Models\Chat\UserChatProgress;

class GenerateAnswer
{
    public function control()
    {
        // Démarrer la session si pas déjà fait
        if (!isset($_SESSION)) {
            session_start();
        }

        $name = strtolower($_REQUEST["name"] ?? '');
        $message = $_REQUEST["message"] ?? '';

        $this->generate($name, $message);
    }

    private function generate(string $name, string $message): void
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => "Tu dois être connecté pour discuter !",
                'progress' => false
            ]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $progressModel = new UserChatProgress();
        
        // Récupérer la progression actuelle
        $currentProgress = $progressModel->getProgressIndex($userId, $name);

        // Charger les réponses depuis le fichier JSON
        $path = __DIR__ . '/../config/answers.json';
        $string = file_get_contents($path);
        $answers = json_decode($string, true);

        // Vérifier si le chat existe
        if (!isset($answers[$name])) {
            echo json_encode([
                'success' => false,
                'message' => "Je ne comprends pas... 🤔",
                'progress' => false
            ]);
            return;
        }

        // Récupérer la réponse attendue pour le niveau actuel
        $expectedAnswer = $answers[$name][(string) $currentProgress] ?? null;

        // Si pas de réponse attendue pour ce niveau, l'utilisateur a terminé
        if ($expectedAnswer === null) {
            echo json_encode([
                'success' => true,
                'message' => "Tu as déjà tout découvert ! Bravo ! 🎉",
                'progress' => false,
                'completed' => true
            ]);
            return;
        }

        // Vérifier si la réponse est correcte (insensible à la casse)
        if (stripos($message, $expectedAnswer) !== false) {
            // Bonne réponse ! Incrémenter la progression
            $progressModel->incrementProgress($userId, $name);
            $newProgress = $currentProgress + 1;

            echo json_encode([
                'success' => true,
                'message' => "Bravo ! Tu as trouvé ! 🎉",
                'progress' => true,
                'newIndex' => $newProgress
            ]);
        } else {
            // Mauvaise réponse
            echo json_encode([
                'success' => false,
                'message' => "Hmm, ce n'est pas ça... Réessaie ! 🤔",
                'progress' => false
            ]);
        }
    }
}
