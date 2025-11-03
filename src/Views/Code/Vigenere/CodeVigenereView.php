<?php

namespace Views\Code\Vigenere;

use Views\AbstractView;

class CodeVigenereView extends AbstractView
{
    // Clé utilisée pour afficher le message encodé
    private const FLASH_KEY = 'FLASH';

    // Chemin du fichier HTML du template de la page de connexion
    private const TEMPLATE_HTML = __DIR__ . '/code-vigenere.html';

    // Méthode qui retourne le chemin du fichier HTML à afficher
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui fournit les variables à injecter dans le template HTML
    public function templateKeys() : array {
        return [
            // Message encodé
            self::FLASH_KEY => flash('Vigenere'),
        ];
    }
}