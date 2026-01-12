<?php

namespace Controllers\Code\Permutation;

use Controllers\AbstractController;
use Views\Code\Permutation\CodePermutationView;

/**
 * Contrôleur pour l'interface de chiffrement par permutation
 * 
 * Gère l'affichage et le traitement du formulaire de chiffrement par permutation.
 */
class EncryptPermutation extends AbstractController
{

    /**
     * Affiche la page de chiffrement par permutation
     * 
     * @return void
     */
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new CodePermutationView();

        // Afficher le contenu de la page
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de chiffrement par permutation
     * 
     * Récupère le texte, la clé, le caractère d'espace et le résultat attendu,
     * puis vérifie si le chiffrement est correct.
     * 
     * @return void
     */
    function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-permutation'];
        $texte_chiffre_a_verifier = $_POST['word-permutation-verify'];
        $cle = $_POST['key-permutation'];
        $char_espaces = $_POST['space-char-permutation'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Permutation::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $cle, $char_espaces);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodePermutationView();

        // Afficher le contenu de la page
        $view->render();

    }

}