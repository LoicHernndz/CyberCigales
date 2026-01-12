<?php

namespace Controllers\Code\Permutation;

use Views\Code\Permutation\DecodePermutationView;

/**
 * Contrôleur pour l'interface de déchiffrement par permutation
 * 
 * Gère l'affichage et le traitement du formulaire de déchiffrement par permutation.
 */
class DecryptPermutation extends EncryptPermutation
{
    /**
     * Affiche la page de déchiffrement par permutation
     * 
     * @return void
     */
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new DecodePermutationView();

        // Afficher le contenu de la page
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de déchiffrement par permutation
     * 
     * Récupère le texte chiffré, la clé et le résultat attendu,
     * puis vérifie si le déchiffrement est correct.
     * 
     * @return void
     */
    function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-permutation'];
        $texte_chiffre_a_verifier = $_POST['word-permutation-verify'];
        $cle = $_POST['key-permutation'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Permutation::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodePermutationView();

        // Afficher le contenu de la page
        $view->render();

    }
}