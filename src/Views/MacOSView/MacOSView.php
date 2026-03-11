<?php

namespace Views\MacOSView;

/**
 * Vue de l'interface MacOS simulée
 *
 * Affiche une interface desktop macOS avec dock, barre de menu et fenêtres.
 */
class MacOSView
{

    /** @var string Chemin vers le template HTML du bureau macOS */
    private const TEMPLATE_HTML = __DIR__ . '/home.html';

    /**
     * Renvoie le chemin du template HTML
     *
     * @return string
     */
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    /**
     * Affiche la page complète du bureau macOS (header + body + footer)
     */
    function render()
    {
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Charge et affiche le template HTML du bureau macOS
     *
     * Si le fichier template n'existe pas, affiche un contenu par défaut.
     */
    function renderBody(): void
    {
        // Supposons que home.html est vide ou ne contient que le fond d'écran.
        // Puisque nous ne l'avons pas, on s'assure que le contenu principal est prêt.
        // Si vous avez un fichier home.html, son contenu sera inséré ici.
        if (file_exists($this->templatePath())) {
            $template = file_get_contents($this->templatePath());

            // Remplacement des clés (si utilisées dans home.html)
            $keys = method_exists($this, 'templateKeys') ? $this->templateKeys() : [];
            foreach ($keys as $key => $value) {
                $template = str_replace("{{{$key}}}", $value, $template);
            }

            echo $template;
        } else {
            // Contenu par défaut si le template n'existe pas, agissant comme le fond d'écran
            echo '
            <div class="absolute inset-0 z-0 flex items-center justify-center text-4xl font-bold text-white/50 pointer-events-none">
                Bureau macOS Simulé
            </div>
            ';
        }
    }

    /**
     * Renvoie les clés de template (vide par défaut)
     *
     * @return array<string, string>
     */
    function templateKeys(): array
    {
        return [];
    }

    /**
     * Affiche le header HTML du bureau macOS (barre de menu Apple, Finder)
     */
    function renderHeader(): void
    {
        $logoHref = isset($_SESSION['user_id']) ? '/dashboard' : '/';

        echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MacOS Personnel</title>

    <!-- Chargement de Tailwind CSS (via CDN) pour le style -->
    <script nonce="' . CSP_NONCE . '" src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="/styles/normalize.css"> <!-- Reset CSS pour la cohérence cross-browser -->
    <link rel="stylesheet" href="/assets/macos/css/macos.css">
    
</head>
<body class="min-h-screen relative">


<header>
    <nav id="menu-bar" class="menu-bar fixed top-0 left-0 right-0 h-7 shadow-sm z-50 flex items-center px-2 text-sm text-gray-800">
        <!-- Menu Apple -->
        <div id="apple-menu" class="group relative hover:bg-gray-200 p-1 rounded-md cursor-pointer mr-4">
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
            <div id="file-menu-toggle" class="hover:bg-gray-200 p-1 rounded-md cursor-pointer">Fichier</div>
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

    /**
     * Affiche le footer HTML du bureau macOS (dock d'applications)
     */
    function renderFooter(): void
    {

        echo '</main>

<footer>
    <div id="dock-container" class="dock-container fixed bottom-4 left-1/2 -translate-x-1/2 p-2 rounded-2xl shadow-2xl">
        <ul id="dock-list" class="flex space-x-5">

            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Finder">
                <img src="/images/macos/logo-du-finder.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Finder</div>
            </li>

            <!-- 2. Navigateur (Web) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Web">
                <img src="/images/macos/safari.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Web</div>
            </li>

            <!-- 3. Mail -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Mail">
                <img src="/images/macos/email.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Mail</div>
            </li>

            <!-- 4. Instagram (Messages) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 from-pink-500 via-red-500 to-yellow-500 rounded-xl flex items-center justify-center relative group" data-app="Instagram">
                <img src="/images/macos/instagram.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Instagram</div>
            </li>

            <!-- Séparateur visuel -->
            <li class="w-px h-10 self-center bg-white/20 mx-3"></li>

            <!-- 6. Calendrier -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex flex-col items-center justify-center relative group" data-app="Calendrier">
                <img src="/images/macos/calendar.png">
            </li>

            <!-- 7. Terminal -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Terminal">
                <img src="/images/macos/terminal.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Terminal</div>
            </li>

            <!-- 8. Paramètres -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Paramètres">
                <img src="/images/macos/ajustement.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Paramètres</div>
            </li>
            
            <!-- 9. Hamming (Mini-jeu) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gradient-to-br from-green-600 to-emerald-800 rounded-xl flex items-center justify-center relative group" data-app="Hamming">
                <svg class="w-9 h-9 text-green-300" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="6" height="6" rx="1" opacity="0.9"/>
                    <rect x="9" y="2" width="6" height="6" rx="1" opacity="0.7"/>
                    <rect x="16" y="2" width="6" height="6" rx="1" opacity="0.9"/>
                    <rect x="2" y="9" width="6" height="6" rx="1" opacity="0.7"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" fill="#ff4444"/>
                    <rect x="16" y="9" width="6" height="6" rx="1" opacity="0.7"/>
                    <rect x="2" y="16" width="6" height="6" rx="1" opacity="0.9"/>
                    <rect x="9" y="16" width="6" height="6" rx="1" opacity="0.7"/>
                    <rect x="16" y="16" width="6" height="6" rx="1" opacity="0.9"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Hamming</div>
            </li>

            <!-- 10. Frequence (Mini-jeu) -->
            <li class="dock-icon w-14 h-14 p-2 bg-gradient-to-br from-blue-600 to-indigo-800 rounded-xl flex items-center justify-center relative group" data-app="Frequence">
                <svg class="w-9 h-9 text-blue-200" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="14" width="4" height="8" rx="1"/>
                    <rect x="8" y="10" width="4" height="12" rx="1"/>
                    <rect x="14" y="6" width="4" height="16" rx="1"/>
                    <rect x="20" y="2" width="4" height="20" rx="1" opacity="0.7"/>
                </svg>
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Frequence</div>
            </li>

            <!-- Separateur visuel -->
            <li class="w-px h-10 self-center bg-white/20 mx-3"></li>

            <!-- 9. Corbeille -->
            <li class="dock-icon w-14 h-14 p-2 bg-gray-400 rounded-xl flex items-center justify-center relative group" data-app="Corbeille">
                <img src="/images/macos/poubelle.png">
                <div class="absolute -top-10 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Corbeille</div>
            </li>
        </ul>
    </div>
</footer>

<script nonce="' . CSP_NONCE . '" src="/assets/macos/js/macos.js"></script>
</body>
</html>';
    }
}