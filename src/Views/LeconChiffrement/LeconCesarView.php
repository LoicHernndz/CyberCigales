<?php

namespace Views\LeconChiffrement;

use Views\AbstractView;

class LeconCesarView extends AbstractView {

    // Chemin du fichier HTML associé à la page des mentions légales
    private const TEMPLATE_HTML = __DIR__ . '/LeconCesar.html';

    // Méthode qui retourne le chemin du template HTML à afficher
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui retourne les variables à injecter dans le template
    // Ici, la page est statique donc aucune donnée dynamique n’est envoyée
    public function templateKeys() : array {
        return [];
    }
}
