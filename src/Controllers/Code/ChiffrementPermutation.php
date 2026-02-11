<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Permutation\CodePermutationView;

class ChiffrementPermutation extends AbstractController
{
    function getMethod(){
        $view = new CodePermutationView();
        $view->render();
    }

    function postMethod(){
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
        $view->render();
    }
}
