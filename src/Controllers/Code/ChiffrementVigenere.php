<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Vigenere\CodeVigenereView;

/**
 * Contrôleur du chiffrement Vigenère
 *
 * Affiche le formulaire de chiffrement Vigenère et traite
 * la vérification du texte chiffré soumis par l'utilisateur.
 */
class ChiffrementVigenere extends AbstractController
{
    /**
     * Affiche le formulaire de chiffrement Vigenère
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new CodeVigenereView();
        $view->render();
    }

    /**
     * Vérifie le chiffrement Vigenère soumis par l'utilisateur
     *
     * Récupère le texte, la clé et la réponse à vérifier depuis POST,
     * puis délègue la vérification au helper Vigenere.
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-vigenere'];
        $cle = $_POST['key-vigenere'];
        $texte_chiffre_a_verifier = $_POST['word-vigenere-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Vigenere::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $cle);
        } else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodeVigenereView();
        $view->render();
    }
}
