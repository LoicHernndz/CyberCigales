<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconVigenere\LeconVigenereView;

/**
 * Contrôleur de la leçon sur le chiffrement Vigenère
 *
 * Affiche le contenu pédagogique expliquant le fonctionnement
 * du chiffrement de Vigenère (chiffrement polyalphabétique).
 */
class Vigenere extends AbstractController
{
    /**
     * Affiche la leçon sur le chiffrement Vigenère
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new LeconVigenereView();
        $view->render();
    }
}
