<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Permutation\PermutationDecryptToolView;

/**
 * Contrôleur de l'outil de déchiffrement par Permutation
 *
 * Affiche l'outil avancé de déchiffrement par permutation,
 * permettant de tester différentes clés et configurations.
 */
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
