<?php

namespace Controllers\Code;

use Views\Code\Vigenere\CodeVigenereView;
use Views\Code\Vigenere\DecodeVigenereView;

class DechiffrementVigenere extends ChiffrementVigenere
{
    public function getMethod(){
        $view = new DecodeVigenereView();
        $view->render();
    }

    public function postMethod(){
        $texte = $_POST['word-vigenere'];
        $cle = $_POST['key-vigenere'];
        $texte_chiffre_a_verifier = $_POST['word-vigenere-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Vigenere::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodeVigenereView();
        $view->render();
    }
}
