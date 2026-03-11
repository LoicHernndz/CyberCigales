<?php
namespace Views;

/**
 * Class abstract contenant les methodes communes a toutes views, par exemple pour avoir le meme footer et header
 */
abstract class AbstractView
{
    /**
     * Recupere le contenu du fichier html associe a la vue pour l'afficher.
     * Dans le fichier html, toutes les parties entre accolades (ex : {FOO}) seront remplaces par de vrais elements html passe à travers la methode templateKeys().
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());

        // Fusionner les clés URL communes avec les clés spécifiques à la vue
        $allKeys = array_merge($this->commonUrlKeys(), $this->templateKeys());

        foreach ($allKeys as $key => $value) {
            // Convertir en string pour éviter les problèmes de type
            $value = (string) $value;
            // Remplacer toutes les occurrences de la clé
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        // Nettoyer les accolades orphelines qui pourraient rester (sécurité)
        $template = preg_replace('/\{\{[A-Z_]+\}\}/', '', $template);

        echo $template;
    }

    /**
     * Renvoie le chemin du fichier html template associe a la view
     */
    abstract function templatePath(): string;

    /**
     * Renvoie une liste des elements dynamiques a ajouter au fichier statique html (exemple : nom d'utilisateur dans la hompage apres s'etre connecte)
     */
    abstract function templateKeys(): array;

    /**
     * Clés URL communes disponibles dans tous les templates HTML.
     */
    protected function commonUrlKeys(): array
    {
        return [
            'URL_HOME'              => url('homepage'),
            'URL_DASHBOARD'         => url('dashboard'),
            'URL_LOGIN'             => url('user_login'),
            'URL_SIGNUP'            => url('user_signup'),
            'URL_LOGOUT'            => url('user_logout'),
            'URL_PROFIL'            => url('user_profil'),
            'URL_EDIT'              => url('user_edit'),
            'URL_RESET_PASSWORD'    => url('user_reset_password'),
            'URL_LECON'             => url('lecon_index'),
            'URL_OUTILS'            => url('outils'),
            'URL_MINIGAMES'         => url('minigames'),
            'URL_INSTAGRAM'         => url('instagram'),
            'URL_MACOS'             => url('macos'),
            'URL_MENTIONS'          => url('mentions'),
            'URL_PLAN'              => url('plan'),
            'URL_LECON_CESAR'       => url('lecon_cesar'),
            'URL_LECON_HIST_MDP'    => url('lecon_hist_mdp'),
            'URL_LECON_VIGENERE'    => url('lecon_vigenere'),
            'URL_LECON_PERMUTATION' => url('lecon_permutation'),
            'URL_LECON_RGPD'        => url('lecon_rgpd'),
            'URL_GAME_HAMMING'      => url('game_hamming'),
            'URL_GAME_FREQUENCY'    => url('game_frequency'),
            'URL_GAME_PHISHING'     => url('game_phishing'),
            'URL_GAME_RESET'        => url('game_reset_game'),
            'URL_CODE_CHIFFREMENT_CESAR'       => url('code_chiffrement_cesar'),
            'URL_CODE_CHIFFREMENT_VIGENERE'    => url('code_chiffrement_vigenere'),
            'URL_CODE_CHIFFREMENT_PERMUTATION' => url('code_chiffrement_permutation'),
            'URL_CODE_OUTIL_PERMUTATION'       => url('code_outil_permutation'),
            'URL_CODE_DECHIFFREMENT_CESAR'       => url('code_dechiffrement_cesar'),
            'URL_CODE_DECHIFFREMENT_VIGENERE'    => url('code_dechiffrement_vigenere'),
            'URL_CODE_DECHIFFREMENT_PERMUTATION' => url('code_dechiffrement_permutation'),
        ];
    }

    /**
     * Affiche la page dans son entierete, footer + contenu (fichier html) + header
     *
     * @return void
     */
    function render()
    {
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Affiche le header de la page
     */
    function renderHeader(): void
    {
        $logoHref = isset($_SESSION['user_id']) ? url('dashboard') : url('homepage');

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
        <link rel="stylesheet" href="/styles/main.css?v=7" type="text/css">
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
        if (isset($_SESSION['user_id'])):
            echo '<a href="' . url('lecon_index') . '" class="nav-link">
                        <span class="material-icons">school</span>
                        <span>Formations</span>
                    </a>
                    <a href="' . url('outils') . '" class="nav-link">
                        <span class="material-icons">build</span>
                        <span>Outils</span>
                    </a>
                    <a href="' . url('minigames') . '" class="nav-link">
                        <span class="material-icons">games</span>
                        <span>Mini jeux</span>
                    </a>
                    <a href="' . url('user_profil') . '" class="nav-link">
                        <span class="material-icons">person</span>
                        <span>Profil</span>
                    </a>
                    <a href="' . url('user_logout') . '" class="nav-link nav-logout">
                        <span class="material-icons">logout</span>
                        <span>Déconnexion</span>
                    </a>';
        else:
            echo '<a href="' . url('homepage') . '" class="nav-link">
                        <span class="material-icons">home</span>
                        <span>Accueil</span>
                    </a>
                    <a href="' . url('user_login') . '" class="nav-link">
                        <span class="material-icons">login</span>
                        <span>Connexion</span>
                    </a>
                    <a href="' . url('user_signup') . '" class="nav-link">
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
                        <a href="' . url('homepage') . '">Accueil</a>
                        <a href="' . url('mentions') . '">Mentions légales</a>
                        <a href="' . url('plan') . '">Plan du site</a>
                    </div>

                    <div class="footer-social">
                        <h4>Suivez-nous</h4>
                        <div class="social-links">
                            <a href="' . url('instagram') . '" aria-label="Instagram" class="social-link">
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
        echo date("Y");
        echo ' CyberCigales. Tous droits réservés.</p>
                </div>
            </footer>
            <script src="/js/mobile-menu.js"></script>
        </body>
    </html>';
    }

}
