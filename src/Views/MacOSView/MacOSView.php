<?php

namespace Views\MacOSView;

class MacOSView {

    // Chemin du fichier HTML associé à la vue d’inscription
    private const TEMPLATE_HTML = __DIR__ . '/home.html';

    // Méthode qui renvoie le chemin du template HTML à utiliser pour cette vue
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    function render(){
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    function renderBody(): void
    {
        // Supposons que home.html est vide ou ne contient que le fond d'écran.
        // Puisque nous ne l'avons pas, on s'assure que le contenu principal est prêt.
        // Si vous avez un fichier home.html, son contenu sera inséré ici.
        if (file_exists($this->templatePath())) {
            $template = file_get_contents($this->templatePath());

            // Remplacement des clés (si utilisées dans home.html)
            $keys = method_exists($this, 'templateKeys') ? $this->templateKeys() : [];
            foreach($keys as $key => $value){
                $template = str_replace("{{{$key}}}", $value, $template);
            }

            echo $template ;
        } else {
            // Contenu par défaut si le template n'existe pas, agissant comme le fond d'écran
            echo '
            <div class="absolute inset-0 z-0 flex items-center justify-center text-4xl font-bold text-white/50 pointer-events-none">
                Bureau macOS Simulé
            </div>
            ';
        }
    }

    // Fonction fictive pour éviter une erreur si elle est appelée par renderBody()
    function templateKeys() : array {
        return [];
    }

    function renderHeader(): void
    {
        $logoHref = isset($_SESSION['user_id']) ? '/dashboard' : '/';

        echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dock Dynamique et Esthétique</title>
    <link rel="stylesheet" href="/styles/normalize.css"> <!-- Reset CSS pour la cohérence cross-browser -->
    <link rel="stylesheet" href="/assets/instagram/css/instagram-fixed.css"> <!-- Styles Instagram corrigés -->
    <link rel="stylesheet" href="/assets/instagram/css/svg-icons.css"> <!-- Styles pour les icônes SVG -->

    <!-- Chargement de Tailwind CSS (via CDN) pour le style -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- STYLES CSS INTÉGRÉS POUR LE DOCK, LA BARRE DE MENUS ET LES FENÊTRES -->
    <style>
        /* ---------------------------------- */
        /* Fond d\'écran (pour le Bureau) */
        /* ---------------------------------- */
        body {
            /* Un fond d\'écran simple pour l\'effet Glassmorphism */
            background: linear-gradient(135deg, #1e3a8a 0%, #171717 100%);
            /* Assurer que le corps prend toute la hauteur de la vue */
            height: 100vh;
        }

        /* ---------------------------------- */
        /* Barre de Menus (Header) */
        /* ---------------------------------- */
        .menu-bar {
            /* Style Glassmorphism pour la barre de menus */
            background-color: rgba(255, 255, 255, 0.5); /* Blanc semi-transparent */
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* ---------------------------------- */
        /* Dock (Footer) */
        /* ---------------------------------- */
        .dock-container {
            /* Style Glassmorphism pour le dock */
            background-color: rgba(255, 255, 255, 0.2); /* Blanc très transparent */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease-in-out;
        }

        /* Effet de loupe au survol du dock (CSS natif) */
        .dock-list:hover .dock-icon {
            transform: scale(1);
        }

        .dock-icon {
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dock-icon:hover {
            transform: scale(1.3) translateY(-10px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
        }

        .dock-icon:hover + .dock-icon {
            transform: scale(1.15); /* Voisins un peu agrandis */
        }
        
        .dock-icon:has(+ .dock-icon:hover) {
            transform: scale(1.15); /* Le voisin précédent est aussi un peu agrandi */
        }

        /* ---------------------------------- */
        /* Fenêtres d\'Applications */
        /* ---------------------------------- */
        .app-window {
            /* Transition pour l\'effet de minimisation/maximisation */
            transition: top 0.3s ease, left 0.3s ease, transform 0.3s ease, opacity 0.3s ease;
            box-sizing: border-box;
        }

        /* Fenêtre au premier plan */
        .active-window {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        
        /* État minimisé (simulé en masquant) */
        .minimized {
            opacity: 0;
            transform: scale(0.5);
            pointer-events: none;
        }
        
        /* Conteneur principal du bureau */
        #desktop {
            height: calc(100vh - 4rem); /* Espace pour la barre de menus et le dock */
            padding-top: 28px; /* Pour éviter que le contenu ne soit sous la barre de menus */
            position: relative;
        }

    </style>
</head>
<body class="min-h-screen relative">


<header>
    <nav id="menu-bar" class="menu-bar fixed top-0 left-0 right-0 h-7 shadow-sm z-50 flex items-center px-2 text-sm text-gray-800">
        <!-- Menu Apple -->
        <div id="apple-menu" class="group relative hover:bg-gray-200 p-1 rounded-md cursor-pointer mr-4" onclick="toggleAppleMenu()">
            <span class="font-semibold text-base"></span>
            <!-- Apple Dropdown -->
            <div id="apple-menu-dropdown" class="absolute top-7 left-0 w-48 bg-white backdrop-blur-sm rounded-lg shadow-xl p-1 hidden text-gray-800 ring-1 ring-gray-300 z-50">
                <div class="hover:bg-blue-500 hover:text-white p-1 rounded-md">À propos de ce Bureau</div>
                <div class="h-px my-1 bg-gray-200"></div>
                <div class="hover:bg-blue-500 hover:text-white p-1 rounded-md">Éteindre...</div>
            </div>
        </div>
    
        <div id="active-app-name" class="font-semibold p-1">Bureau</div>
        <div class="ml-4 relative">
            <div id="file-menu-toggle" class="hover:bg-gray-200 p-1 rounded-md cursor-pointer" onclick="toggleFileMenu()">Fichier</div>
            <!-- Dropdown pour Fichier -->
            <div id="file-menu-dropdown" class="absolute top-7 left-0 w-48 bg-white backdrop-blur-sm rounded-lg shadow-xl p-1 hidden text-gray-800 ring-1 ring-gray-300 z-50">
                <div class="hover:bg-blue-500 hover:text-white p-1 rounded-md flex justify-between items-center">
                    <span>Ouvrir</span>
                    <span class="text-xs text-gray-400">⌘O</span>
                </div>
                <div class="h-px my-1 bg-gray-200"></div>
                <div class="p-1 rounded-md text-gray-400 cursor-default flex justify-between items-center">
                    <span>Enregistrer</span>
                    <span class="text-xs text-gray-400">⌘S</span>
                </div>
            </div>
        </div>
        <div class="hover:bg-gray-200 p-1 rounded-md cursor-pointer">Édition</div>
        <div class="hover:bg-gray-200 p-1 rounded-md cursor-pointer">Présentation</div>

        <!-- Éléments de Statut -->
        <div class="ml-auto flex items-center space-x-3">
            <span class="text-xs text-gray-700">FR</span>
            <!-- Heure/Date (mis à jour par JS) -->
            <span id="time-display" class="font-medium"></span>
        </div>
    </nav>
</header>

<main id="desktop">
';
    }

    function renderFooter(): void
    {

        echo '</main>

<footer>
    <div id="dock-container" class="dock-container fixed bottom-4 left-1/2 -translate-x-1/2 p-2 rounded-2xl shadow-2xl">
        <ul id="dock-list" class="flex space-x-5">

            <li class="dock-icon w-14 h-14 p-2 bg-white rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Finder\')">
                <svg class="w-8 h-8 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-7-8a7 7 0 1114 0 7 7 0 01-14 0z"/>
                    <path d="M21 21l-4.35-4.35"/>
                    <path fill-rule="evenodd" d="M13 3a1 1 0 011 1v6a1 1 0 11-2 0V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Finder</div>
            </li>

            <!-- 2. Navigateur (Web) -->
            <li class="dock-icon w-14 h-14 p-2 bg-white rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Web\')">
                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm-3.5-8.5a1 1 0 112 0 1 1 0 01-2 0zm5 0a1 1 0 112 0 1 1 0 01-2 0zm-2.5-4a1 1 0 011-1 4 4 0 014 4h-2a2 2 0 00-2-2 1 1 0 01-1-1z"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Web</div>
            </li>

            <!-- 3. Mail -->
            <li class="dock-icon w-14 h-14 p-2 bg-red-600 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Mail\')">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10z"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Mail</div>
            </li>

            <!-- 4. Instagram (Messages) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Instagram\')">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="5" ry="5"></rect>
                    <path d="M16 8h.01"></path>
                    <circle cx="12" cy="12" r="4"></circle>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Instagram</div>
            </li>

            <!-- 5. X (Twitter) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-900 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'X\')">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.9 3.1L12 10.2L5.1 3.1H2.5L9.6 10.2L2.5 17.3H5.1L12 10.2L18.9 17.3H21.5L14.4 10.2L21.5 3.1H18.9Z"></path>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">X (Twitter)</div>
            </li>

            <!-- Séparateur visuel -->
            <li class="w-px h-10 self-center bg-white/20 mx-3"></li>

            <!-- 6. Calendrier -->
            <li class="dock-icon w-14 h-14 p-2 bg-blue-500 rounded-xl flex flex-col items-center justify-center relative group" onclick="openApp(\'Calendrier\')">
                <div class="text-xs text-white -mb-1">NOV</div>
                <div class="text-xl font-bold text-white leading-none">03</div>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Calendrier</div>
            </li>

            <!-- 7. Terminal -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-800 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Terminal\')">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M7 10l3 3 3-3M7 14h10"></path>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Terminal</div>
            </li>

            <!-- 8. Paramètres -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Paramètres\')">
                <svg class="w-8 h-8 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm-1-7h2v4h-2zm0-6h2v4h-2z"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Paramètres</div>
            </li>

            <!-- Séparateur visuel -->
            <li class="w-px h-10 self-center bg-white/20 mx-3"></li>

            <!-- 9. Corbeille -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-700 rounded-xl flex items-center justify-center relative group" onclick="openApp(\'Corbeille\')">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6m4-6v6"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Corbeille</div>
            </li>
        </ul>
    </div>
</footer>

<script src="/assets/macos/js/macos.js"></script>
</body>
</html>';
    }
}
