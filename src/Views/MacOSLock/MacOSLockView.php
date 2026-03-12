<?php

namespace Views\MacOSLock;

/**
 * Vue de l'écran de verrouillage macOS
 *
 * Affichée quand l'utilisateur tente d'accéder à /macos sans être connecté.
 * Style identique à l'écran de verrouillage macOS authentique.
 */
class MacOSLockView
{
    /** @var string Chemin vers le template HTML de l'écran de verrouillage */
    private const TEMPLATE_HTML = __DIR__ . '/macos-lock.html';

    /**
     * Affiche l'écran de verrouillage macOS
     *
     * @return void
     */
    public function render(): void
    {
        echo file_get_contents(self::TEMPLATE_HTML);
    }
}
