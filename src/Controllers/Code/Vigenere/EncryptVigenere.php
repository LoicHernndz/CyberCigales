<?php

namespace Controllers\Code\Vigenere;

use Controllers\AbstractController;
use Views\Code\Vigenere\CodeVigenereView;

/**
 * Contrôleur pour l'interface de chiffrement Vigenère
 * 
 * Gère l'affichage et le traitement du formulaire de chiffrement Vigenère.
 */
class EncryptVigenere extends AbstractController
{
    /**
     * Affiche la page de chiffrement Vigenère
     * 
     * @return void
     */
    public function getMethod(){
        $view = new CodeVigenereView();
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de chiffrement Vigenère
     * 
     * Récupère le texte, la clé et le résultat attendu depuis le formulaire,
     * puis vérifie si le chiffrement est correct.
     * 
     * @return void
     */
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