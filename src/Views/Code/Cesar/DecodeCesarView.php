<?php

namespace Views\Code\Cesar;

use Views\AbstractView;

class DecodeCesarView extends AbstractView {

    // Clé utilisée pour afficher les messages flash (succès ou erreurs d’inscription)
    private const FLASH_KEY = 'FLASH';

    // Chemin du fichier HTML associé à la vue de déchiffrement cesar
    private const TEMPLATE_HTML = __DIR__ . '/decode-cesar.html';

    // Méthode qui renvoie le chemin du template HTML à utiliser pour cette vue
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui définit les variables à injecter dans le template HTML
    public function templateKeys() : array {
        return [
            self::FLASH_KEY => flash('Cesar')
        ];
    }
}