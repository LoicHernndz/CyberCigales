<?php

namespace Views\User\Login;

use Views\AbstractView;

/**
 * Vue du formulaire de connexion
 * 
 * Affiche le formulaire de login avec les messages flash.
 */
class LoginView extends AbstractView
{

    // Clé utilisée pour afficher les messages flash (erreurs, alertes, succès)
    private const FLASH_KEY = 'FLASH';

    // Chemin du fichier HTML du template de la page de connexion
    private const TEMPLATE_HTML = __DIR__ . '/login.html';

    // Méthode qui retourne le chemin du fichier HTML à afficher
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui fournit les variables à injecter dans le template HTML
    public function templateKeys(): array
    {
        return [
                // Message flash stocké dans la session (ex: "Mauvais mot de passe", "Connexion réussie", etc.)
            self::FLASH_KEY => flash('login'),
            'CAPTCHA_TS' => (string) time(),
            // OWASP A01 : token CSRF injecté dans le formulaire pour empêcher les requêtes forgées
            'CSRF_FIELD' => csrf_field()
        ];
    }
}
