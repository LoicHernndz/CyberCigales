<?php

namespace Controllers\Code\Permutation;

use Controllers\AbstractController;
use Views\Code\Permutation\CodePermutationView;

class EncryptPermutation extends AbstractController
{

    // Méthode principale exécutée lorsque la route "/user/profil" est appelée en GET
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new CodePermutationView();

        // Afficher le contenu de la page
        $view->render();
    }

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