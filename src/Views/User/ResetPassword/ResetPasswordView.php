<?php

namespace Views\User\ResetPassword;

use Views\AbstractView;

class ResetPasswordView extends AbstractView {

    // Clé utilisée pour afficher les messages flash
    private const FLASH_KEY = 'FLASH';

    // Chemin du fichier HTML associé à cette vue
    private const TEMPLATE_HTML = __DIR__ . '/reset-password.html';

    // Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui retourne les variables à injecter dans le template HTML
    public function templateKeys() : array {
        return [
            // Message flash à afficher (par exemple : "Un e-mail vous a été envoyé" ou "Adresse invalide")
            self::FLASH_KEY => flash('reset'),
            // OWASP A01 : token CSRF injecté dans le formulaire pour empêcher les requêtes forgées
            'CSRF_FIELD' => csrf_field()
        ];
    }
}
