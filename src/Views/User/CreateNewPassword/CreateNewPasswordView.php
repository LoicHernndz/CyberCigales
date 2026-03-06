<?php

namespace Views\User\CreateNewPassword;

use Views\AbstractView;

class CreateNewPasswordView extends AbstractView
{
    // Clé utilisée pour afficher les messages flash (succès ou erreur)
    private const FLASH_KEY = 'FLASH';

    // Clés utilisées pour passer le selector et le validator au template
    private const SELECTOR_KEY = 'SELECTOR';
    private const VALIDATOR_KEY = 'VALIDATOR';

    // Chemin du fichier HTML associé à cette vue
    private const TEMPLATE_HTML = __DIR__ . '/create-new-password.html';

    // Méthode qui renvoie le chemin du fichier HTML à utiliser pour le rendu
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui renvoie les variables à injecter dans le template HTML
    public function templateKeys() : array {
        return [
            // Message flash à afficher (erreurs ou confirmations)
            self::FLASH_KEY => flash('new-password'),

            // Les valeurs du selector et validator extraites de l’URL (nettoyées avec trim)
            self::SELECTOR_KEY => trim($_GET['selector']),
            self::VALIDATOR_KEY => trim($_GET['validator']),
            // OWASP A01 : token CSRF injecté dans le formulaire pour empêcher les requêtes forgées
            'CSRF_FIELD' => csrf_field()
        ];
    }
}
