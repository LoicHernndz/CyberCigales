<?php
namespace Views\Code\Hamming;

use Views\AbstractView;

/**
 * Vue pour l'outil Hamming (page classique sans Ajax)
 */
class HammingToolView extends AbstractView
{
    private array $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    function templatePath(): string
    {
        return __DIR__ . '/hamming-tool.html';
    }
    
    function templateKeys(): array
    {
        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        $message = $this->data['message'] ?? '';
        $messageType = $this->data['messageType'] ?? '';
        
        // Generer le HTML du carre avec formulaires
        $squareHtml = $this->generateSquareHtml($square);
        
        // Message flash
        $flashHtml = '';
        if (!empty($message)) {
            $flashClass = $messageType === 'success' ? 'flash-success' : 'flash-error';
            $flashHtml = '<div class="flash-message ' . $flashClass . '">' . htmlspecialchars($message) . '</div>';
        }
        
        return [
            'SQUARE_HTML' => $squareHtml,
            'FLASH' => $flashHtml
        ];
    }
    
    /**
     * Genere le HTML du carre 3x3 avec boutons de formulaire
     */
    private function generateSquareHtml(array $square): string
    {
        $html = '<div class="hamming-grid">';
        
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $bit = $square[$row][$col];
                $html .= '
                <form method="post" action="/code/hamming" class="bit-form">
                    <input type="hidden" name="row" value="' . $row . '">
                    <input type="hidden" name="col" value="' . $col . '">
                    <button type="submit" class="bit-cell">' . $bit . '</button>
                </form>';
            }
        }
        
        $html .= '</div>';
        return $html;
    }
    
    function renderHeader(): void
    {
        $logoHref = isset($_SESSION['user_id']) ? '/dashboard' : '/';
        
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carre de Hamming - CyberCigales</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/styles/main.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/header.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/auth.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/hamming-tool.css?v=1" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    </head>
    <body>
        <header class="site-header">
            <div class="header-container">
                <div class="logo">
                    <a href="' . $logoHref . '">
                        <span class="material-icons logo-icon">security</span>
                        <span class="logo-text">CyberCigales</span>
                    </a>
                </div>
                <nav class="main-nav">';
        
        if(isset($_SESSION['user_id'])) :
            echo '
                    <a href="/" class="nav-link">
                        <span class="material-icons">school</span>
                        <span>Formations</span>
                    </a>
                    <a href="/user/profil" class="nav-link">
                        <span class="material-icons">person</span>
                        <span>Profil</span>
                    </a>
                    <a href="/user/logout" class="nav-link nav-logout">
                        <span class="material-icons">logout</span>
                        <span>Deconnexion</span>
                    </a>';
        else :
            echo '
                    <a href="/" class="nav-link">
                        <span class="material-icons">home</span>
                        <span>Accueil</span>
                    </a>
                    <a href="/user/login" class="nav-link">
                        <span class="material-icons">login</span>
                        <span>Connexion</span>
                    </a>
                    <a href="/user/signup" class="nav-link">
                        <span class="material-icons">person_add</span>
                        <span>Inscription</span>
                    </a>';
        endif;
        
        echo '
                </nav>
            </div>
        </header>
        <main class="main-content">';
    }
}

