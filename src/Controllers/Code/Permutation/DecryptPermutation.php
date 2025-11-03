<?php

namespace Controllers\Code\Permutation;

use Views\Code\Permutation\DecodePermutationView;

class DecryptPermutation extends EncryptPermutation
{
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new DecodePermutationView();

        // Afficher le contenu de la page
        $view->render();
    }

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