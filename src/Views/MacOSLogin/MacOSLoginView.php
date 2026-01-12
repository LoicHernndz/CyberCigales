<?php

namespace Views\MacOSLogin;

/**
 * Vue pour la page de connexion macOS
 * Ne hérite pas de AbstractView car c'est une page immersive sans header/footer
 */
class MacOSLoginView
{
    /**
     * Affiche la page de connexion macOS
     */
    public function show(): void
    {
        // Inclure directement le fichier HTML
        include __DIR__ . '/macos-login.html';
    }
}

