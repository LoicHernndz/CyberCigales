<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Permutation\PermutationDecryptToolView;
use Attributes\Route;

/**
 * Contrôleur de l'outil de déchiffrement par Permutation
 *
 * Affiche l'outil avancé de déchiffrement par permutation,
 * permettant de tester différentes clés et configurations.
 */
#[Route('/code/outil-permutation', name: 'code_outil_permutation')]
class OutilPermutation extends AbstractController
{
    /**
     * Affiche l'outil de déchiffrement par permutation
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new PermutationDecryptToolView();
        $view->render();
    }
}
