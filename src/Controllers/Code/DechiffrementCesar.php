<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Cesar\DecodeCesarView;
use Attributes\Route;

/**
 * Contrôleur du déchiffrement César
 *
 * Affiche le formulaire de déchiffrement César et traite
 * la vérification du texte déchiffré soumis par l'utilisateur.
 */
#[Route('/code/dechiffrement-cesar', name: 'code_dechiffrement_cesar')]
class DechiffrementCesar extends AbstractController
{
    /**
     * Affiche le formulaire de déchiffrement César
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new DecodeCesarView();
        $view->render();
    }

    /**
     * Vérifie le déchiffrement César soumis par l'utilisateur
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];
        if (!isset($_POST['btn-submit'])) {
            \Services\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $decalage);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodeCesarView();
        $view->render();
    }
}
