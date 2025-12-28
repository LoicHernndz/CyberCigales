<?php
namespace Views\UnilateralDiscussion;

use Views\AbstractView;

class UnilateralDiscussionView extends AbstractView
{
    private array $templateKeys = [];
    
    public function addTemplateKey(string $key, string $value): void
    {
        $this->templateKeys[$key] = $value;
    }
    
    function templatePath(): string
    {
        return __DIR__ . '/unilateral-discussion.html';
    }
    
    function templateKeys(): array
    {
        return $this->templateKeys;
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
        <meta name="description" content="CyberCigales : Discussion unilatérale">
        <title>Discussion Unilatérale - CyberCigales</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/styles/immersive-utils.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/main.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/header.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/unilateral-discussion.css?v=1" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
        <link rel="shortcut icon" href="/images/favicon.svg">
        <link rel="apple-touch-icon" href="/images/favicon.svg">
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
                <nav class="main-nav">
            ';
        if(isset($_SESSION['user_id'])) :
            echo '<a href="/" class="nav-link">
                        <span class="material-icons">school</span>
                        <span>Formations</span>
                    </a>
                    <a href="/user/profil" class="nav-link">
                        <span class="material-icons">person</span>
                        <span>Profil</span>
                    </a>
                    <a href="/user/logout" class="nav-link nav-logout">
                        <span class="material-icons">logout</span>
                        <span>Déconnexion</span>
                    </a>';
        else :
            echo '<a href="/" class="nav-link">
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
                <button class="mobile-menu-toggle" aria-label="Menu">
                    <span class="material-icons">menu</span>
                </button>
            </div>
        </header>
        <main class="main-content">';
    }
}

