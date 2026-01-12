<?php
namespace Controllers\Code\Cesar;

use Controllers\AbstractController;
use Views\Code\Cesar\CodeCesarView;

/**
 * Contrôleur pour l'interface de chiffrement César
 * 
 * Gère l'affichage et le traitement du formulaire de chiffrement César.
 */
class EncryptCesar extends AbstractController
{

    /**
     * Affiche la page de chiffrement César
     * 
     * @return void
     */
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new CodeCesarView();

        // Afficher le contenu de la page
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de chiffrement César
     * 
     * Récupère le texte, le décalage et le résultat attendu depuis le formulaire,
     * puis vérifie si le chiffrement est correct.
     * 
     * @return void
     */
    function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $decalage);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodeCesarView();

        // Afficher le contenu de la page
        $view->render();

    }

}
