<?php

namespace Views\User\Signup;

use Views\AbstractView;

class SignupView extends AbstractView {

    // Clé utilisée pour afficher les messages flash (succès ou erreurs d’inscription)
    private const FLASH_KEY = 'FLASH';

    // Chemin du fichier HTML associé à la vue d’inscription
    private const TEMPLATE_HTML = __DIR__ . '/signup.html';

    // Méthode qui renvoie le chemin du template HTML à utiliser pour cette vue
    public function templatePath() : string {
        return self::TEMPLATE_HTML; 
    }

    // Méthode qui définit les variables à injecter dans le template HTML
    public function templateKeys() : array {
        return [
            // Message flash stocké dans la session (ex: "Email déjà utilisé", "Inscription réussie", etc.)
            self::FLASH_KEY => flash('signup')
        ];
    }
}
