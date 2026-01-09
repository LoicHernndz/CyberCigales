<?php
namespace Views;

/**
 * Class abstract contenant les methodes communes a toutes views, par exemple pour avoir le meme footer et header
 */
abstract class AbstractView {
    /**
     * Recupere le contenu du fichier html associe a la vue pour l'afficher.
     * Dans le fichier html, toutes les parties entre accolades (ex : {FOO}) seront remplaces par de vrais elements html passe à travers la methode templateKeys().
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());

        foreach($this->templateKeys() as $key => $value){
            $template = str_replace("{{{$key}}}", $value, $template);
        }

        echo $template ;
    }

    /**
     * Renvoie le chemin du fichier html template associe a la view
     */
    abstract function templatePath() : string ;

    /**
     * Renvoie une liste des elements dynamiques a ajouter au fichier statique html (exemple : nom d'utilisateur dans la hompage apres s'etre connecte)
     */
    abstract function templateKeys() : array ;

    /**
     * Affiche la page dans son entierete, footer + contenu (fichier html) + header
     */
    function render(){
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Affiche le header de la page
     */
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
        <meta name="description" content="CyberCigales : Escape Game Numérique autour de la cybersécurité et de la cryptographie.">
        <title>CyberCigales</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/styles/immersive-utils.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/main.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/header.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/auth.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/landing.css?v=5" type="text/css">
        <link rel="stylesheet" href="/styles/rgpd-presentation.css?v=5" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
        <link rel="shortcut icon" href="/images/favicon.svg">
        <link rel="apple-touch-icon" href="/images/favicon.svg">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="CyberCigales">
        <meta property="og:title" content="CyberCigales">
        <meta property="og:description" content="Escape Game Numérique autour de la cybersécurité et de la cryptographie.">
        <meta property="og:image" content="https://cybercigales.fr/images/cybercigales-logo.png?v=4">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="512">
        <meta property="og:image:height" content="512">
        <meta property="og:image:alt" content="Logo CyberCigales - Cigale avec cadenas">
        <meta property="og:url" content="https://cybercigales.fr">
        <meta name="twitter:card" content="summary_large_image">
        <meta property="og:image" content="/images/favicon.svg">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="CyberCigales">
        <meta name="twitter:description" content="Escape Game Numérique autour de la cybersécurité et de la cryptographie.">
        <meta name="twitter:image" content="https://cybercigales.fr/images/cybercigales-logo.png?v=4">
        <meta name="twitter:image:alt" content="Logo CyberCigales - Cigale avec cadenas">
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
            echo '<a href="/lecon" class="nav-link">
                        <span class="material-icons">school</span>
                        <span>Formations</span>
                    </a>
                    <a href="/minigames" class="nav-link">
                        <span class="material-icons">games</span>
                        <span>Mini jeux</span>
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

    /**
     * Affiche le footer de la page
     */
    function renderFooter(): void
    {

        echo '</main>
               <footer class="site-footer">
                <div class="footer-container">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <span class="material-icons">security</span>
                            <span>CyberCigales</span>
                        </div>
                        <p class="footer-tagline">Votre plateforme de sensibilisation à la cybersécurité</p>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Navigation</h4>
                        <a href="/">Accueil</a>
                        <a href="/mentions">Mentions légales</a>
                        <a href="/plan">Plan du site</a>
                    </div>
                    
                    <div class="footer-social">
                        <h4>Suivez-nous</h4>
                        <div class="social-links">
                            <a href="/instagram" aria-label="Instagram" class="social-link">
                                <span class="material-icons">photo_camera</span>
                            </a>
                            <a href="https://www.facebook.com/" target="_blank" rel="noopener" aria-label="Facebook" class="social-link">
                                <span class="material-icons">facebook</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; ';
        echo        date("Y");
        echo ' CyberCigales. Tous droits réservés.</p>
                </div>
            </footer>
            <!-- Script header-effects.js supprimé car non utilisé -->
        </body>
    </html>';
    }

}