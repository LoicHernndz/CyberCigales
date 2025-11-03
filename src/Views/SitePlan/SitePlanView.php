<?php

namespace Views\SitePlan;

use Views\AbstractView;

class SitePlanView extends AbstractView {

    // Chemin du fichier HTML associé à la page du plan du site
    private const TEMPLATE_HTML = __DIR__ . '/site-plan.html';

    // Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui retourne les variables à injecter dans le template
    // Ici, la page est statique : aucun contenu dynamique n’est nécessaire
    public function templateKeys() : array {
        return [];
    }
}
