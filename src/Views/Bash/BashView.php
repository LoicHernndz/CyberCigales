<?php

namespace Views\Bash;

use Views\AbstractView;

/**
 * Vue du terminal SSH simule
 *
 * Affiche l'interface de terminal en plein ecran (sans header/footer).
 * Le joueur doit se connecter via SSH avant d'acceder au filesystem.
 */
class BashView extends AbstractView
{
    private const TEMPLATE_HTML = __DIR__ . '/bash.html';

    /**
     * Retourne le chemin du template HTML du terminal
     *
     * @return string Chemin absolu du fichier HTML
     */
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    /**
     * Retourne les variables dynamiques a injecter dans le template
     *
     * Page statique : aucune donnee dynamique.
     *
     * @return array Tableau vide
     */
    public function templateKeys(): array
    {
        return [];
    }

    /**
     * Affiche le terminal sans header/footer (plein ecran)
     *
     * @return void
     */
    public function render()
    {
        parent::renderBody();
    }
}
