<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Cesar\CodeCesarView;

/**
 * Contrôleur du chiffrement César
 *
 * Affiche le formulaire de chiffrement César et traite la vérification
 * du texte chiffré soumis par l'utilisateur.
 */
class ChiffrementCesar extends AbstractController
{
    /**
     * Affiche le formulaire de chiffrement César
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new CodeCesarView();
        $view->render();
    }

    /**
     * Vérifie le chiffrement César soumis par l'utilisateur
     *
     * Récupère le texte, le décalage et la réponse à vérifier depuis POST,
     * puis délègue la vérification au helper Cesar.
     *
     * @return void
     */
    public function postMethod(): void
    {
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $decalage);
        } else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodeCesarView();
        $view->render();
    }
}
