<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconVigenere\LeconVigenereView;
use Attributes\Route;

/**
 * Contrôleur de la leçon sur le chiffrement Vigenère
 *
 * Affiche le contenu pédagogique expliquant le fonctionnement
 * du chiffrement de Vigenère (chiffrement polyalphabétique).
 */
#[Route('/lecon/vigenere', name: 'lecon_vigenere')]
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
