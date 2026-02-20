<?php

namespace Controllers\Code;

use Views\Code\Permutation\DecodePermutationView;

/**
 * Contrôleur du déchiffrement par Permutation
 *
 * Affiche le formulaire de déchiffrement par permutation et traite
 * la vérification du texte déchiffré soumis par l'utilisateur.
 * Hérite de ChiffrementPermutation pour réutiliser la logique commune.
 */
class DechiffrementPermutation extends ChiffrementPermutation
{
    /**
     * Affiche le formulaire de déchiffrement par permutation
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new DecodePermutationView();
        $view->render();
    }

    /**
     * Vérifie le déchiffrement par permutation soumis par l'utilisateur
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-permutation'];
        $texte_chiffre_a_verifier = $_POST['word-permutation-verify'];
        $cle = $_POST['key-permutation'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Permutation::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        } else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodePermutationView();
        $view->render();
    }
}
