<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconVigenere\LeconVigenereView;
use Controllers\AbstractController;

/**
 * Contrôleur de la leçon sur le chiffrement de Vigenère
 * 
 * Affiche la leçon pédagogique sur le chiffrement polyalphabétique de Vigenère.
 */
class LeconVigenere extends AbstractController
{

    /**
     * Affiche la leçon sur le chiffrement de Vigenère
     * 
     * @return void
     */
    function getMethod()
    {
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconVigenereView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
