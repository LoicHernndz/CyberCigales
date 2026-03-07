<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconPermutation\LeconPermutationView;
use Attributes\Route;

/**
 * Contrôleur de la leçon sur le chiffrement par permutation
 *
 * Affiche le contenu pédagogique expliquant le fonctionnement
 * du chiffrement par permutation (substitution polyalphabétique).
 */
#[Route('/lecon/permutation', name: 'lecon_permutation')]
class Permutation extends AbstractController
{
    /**
     * Affiche la leçon sur le chiffrement par permutation
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new LeconPermutationView();
        $view->render();
    }
}
