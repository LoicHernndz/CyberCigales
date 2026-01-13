<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconPermutation\LeconPermutationView;
use Controllers\AbstractController;

/**
 * Contrôleur de la leçon sur le chiffrement par permutation
 * 
 * Affiche la leçon pédagogique sur le chiffrement par transposition de colonnes.
 */
class LeconPermutation extends AbstractController
{

    /**
     * Affiche la leçon sur le chiffrement par permutation
     * 
     * @return void
     */
    function getMethod()
    {
        // Création d’une instance de la vue "MentionsView"
        $view = new LeconPermutationView();
        // Affichage de la page des mentions légales
        $view->render();
    }
}
