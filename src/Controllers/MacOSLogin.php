<?php

namespace Controllers;

use Views\MacOSLogin\MacOSLoginView;

class MacOSLogin extends AbstractController
{
    /**
     * Méthode appelée par le routeur
     */
    public function control(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->getMethod();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postMethod();
        }
    }

    /**
     * Affiche la page de connexion macOS
     */
    public function getMethod(): void
    {
        // Si déjà connecté, rediriger vers macOS
        if (self::isLoggedIn()) {
            header('Location: /macos');
            exit;
        }

        $view = new MacOSLoginView();
        $view->show();
    }

    /**
     * Gère la connexion (POST)
     */
    public function postMethod(): void
    {
        // Vérifier que c'est une demande de connexion
        if (isset($_POST['action']) && $_POST['action'] === 'login') {
            $password = $_POST['password'] ?? '';
            $correctPassword = 'cybersecurite';

            if ($password === $correctPassword) {
                // Enregistrer dans la session
                self::login();
                
                // Réponse de succès
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                // Mot de passe incorrect
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Requête invalide']);
        }
    }

    /**
     * Vérifie le mot de passe et connecte l'utilisateur (endpoint AJAX)
     */
    public function verify(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $password = $data['password'] ?? '';

        // Mot de passe correct
        $correctPassword = 'cybersecurite';

        if ($password === $correctPassword) {
            self::login();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['macos_logged_in']) && $_SESSION['macos_logged_in'] === true;
    }

    /**
     * Connecte l'utilisateur
     */
    public static function login(): void
    {
        $_SESSION['macos_logged_in'] = true;
    }

    /**
     * Déconnecte l'utilisateur
     */
    public static function logout(): void
    {
        unset($_SESSION['macos_logged_in']);
        header('Location: /macos-login');
        exit;
    }
}

