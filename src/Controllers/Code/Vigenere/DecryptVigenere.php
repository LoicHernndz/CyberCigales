<?php

namespace Controllers\Code\Vigenere;
use Views\Code\Vigenere\CodeVigenereView;
use Views\Code\Vigenere\DecodeVigenereView;

/**
 * Contrôleur pour l'interface de déchiffrement Vigenère
 * 
 * Gère l'affichage et le traitement du formulaire de déchiffrement Vigenère.
 */
class DecryptVigenere extends EncryptVigenere
{
    /**
     * Affiche la page de déchiffrement Vigenère
     * 
     * @return void
     */
    public function getMethod(){
        $view = new DecodeVigenereView();
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de déchiffrement Vigenère
     * 
     * Récupère le texte chiffré, la clé et le résultat attendu depuis le formulaire,
     * puis vérifie si le déchiffrement est correct.
     * 
     * @return void
     */
    public function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-vigenere'];
        $cle = $_POST['key-vigenere'];
        $texte_chiffre_a_verifier = $_POST['word-vigenere-verify'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Vigenere::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $cle);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }

        $view = new CodeVigenereView();

        // Afficher le contenu de la page
        $view->render();
    }
}