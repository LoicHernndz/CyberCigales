<?php

namespace Controllers\Code\Vigenere;

use Controllers\AbstractController;
use Views\Code\Vigenere\CodeVigenereView;

class EncryptVigenere extends AbstractController
{
    public function getMethod(){
        $view = new CodeVigenereView();
        $view->render();
    }

    public function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-vigenere'];
        $cle = $_POST['key-vigenere'];
        $texte_chiffre_a_verifier = $_POST['word-vigenere-verify'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Vigenere::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $cle);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }

        $view = new CodeVigenereView();

        // Afficher le contenu de la page
        $view->render();
    }
}