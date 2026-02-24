<?php

namespace Controllers\Code;

use Views\Code\Vigenere\DecodeVigenereView;

/**
 * Contrôleur du déchiffrement Vigenère
 *
 * Affiche le formulaire de déchiffrement Vigenère et traite
 * la vérification du texte déchiffré soumis par l'utilisateur.
 * Hérite de ChiffrementVigenere pour réutiliser la logique commune.
 */
class DechiffrementVigenere extends ChiffrementVigenere
{
    /**
     * Affiche le formulaire de déchiffrement Vigenère
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new DecodeVigenereView();
        $view->render();
    }

    /**
     * Vérifie le déchiffrement Vigenère soumis par l'utilisateur
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-vigenere'];
        $cle = $_POST['key-vigenere'];
        $texte_chiffre_a_verifier = $_POST['word-vigenere-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Vigenere::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        } else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodeVigenereView();
        $view->render();
    }
}
