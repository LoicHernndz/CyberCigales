<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Permutation\CodePermutationView;
use Attributes\Route;

/**
 * Contrôleur du chiffrement par Permutation
 *
 * Affiche le formulaire de chiffrement par permutation et traite
 * la vérification du texte chiffré soumis par l'utilisateur.
 */
#[Route('/code/chiffrement-permutation', name: 'code_chiffrement_permutation')]
class ChiffrementPermutation extends AbstractController
{
    /**
     * Affiche le formulaire de chiffrement par permutation
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new CodePermutationView();
        $view->render();
    }

    /**
     * Vérifie le chiffrement par permutation soumis par l'utilisateur
     *
     * Récupère le texte, la clé, le caractère d'espacement et la réponse
     * à vérifier depuis POST, puis délègue la vérification au helper Permutation.
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-permutation'];
        $texte_chiffre_a_verifier = $_POST['word-permutation-verify'];
        $cle = $_POST['key-permutation'];
        $char_espaces = $_POST['space-char-permutation'];
        if (!isset($_POST['btn-submit'])) {
            \Services\Code\Permutation::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $cle, $char_espaces);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodePermutationView();
        $view->render();
    }
}
