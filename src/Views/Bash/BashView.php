<?php

namespace Views\Bash;

use Views\AbstractView;

/**
 * Vue du terminal Bash simulé
 * 
 * Affiche l'interface de terminal en ligne de commande.
 */
class BashView extends AbstractView
{
    // Chemin du fichier HTML associé à la page des mentions légales
    private const TEMPLATE_HTML = __DIR__ . '/bash.html';

    // Méthode qui retourne le chemin du template HTML à afficher
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui retourne les variables à injecter dans le template
    // Ici, la page est statique donc aucune donnée dynamique n’est envoyée
    public function templateKeys(): array
    {
        return [];
    }

    public function render()
    {
        parent::renderBody();
    }
}