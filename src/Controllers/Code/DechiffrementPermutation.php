<?php

namespace Controllers\Code;

use Views\Code\Permutation\DecodePermutationView;

class DechiffrementPermutation extends ChiffrementPermutation
{
    function getMethod(){
        $view = new DecodePermutationView();
        $view->render();
    }

    function postMethod(){
        $texte = $_POST['word-permutation'];
        $texte_chiffre_a_verifier = $_POST['word-permutation-verify'];
        $cle = $_POST['key-permutation'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Permutation::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodePermutationView();
        $view->render();
    }
}
