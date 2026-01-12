<?php
namespace Controllers\Code\Cesar;

use Controllers\AbstractController;
use Views\Code\Cesar\DecodeCesarView;

/**
 * Contrôleur pour l'interface de déchiffrement César
 * 
 * Gère l'affichage et le traitement du formulaire de déchiffrement César.
 */
class DecryptCesar extends AbstractController
{

    /**
     * Affiche la page de déchiffrement César
     * 
     * @return void
     */
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new DecodeCesarView();

        // Afficher le contenu de la page
        $view->render();
    }

    /**
     * Traite la soumission du formulaire de déchiffrement César
     * 
     * Récupère le texte chiffré, le décalage et le résultat attendu depuis le formulaire,
     * puis vérifie si le déchiffrement est correct.
     * 
     * @return void
     */
    function postMethod(){
        // Récupère les variables de chiffrement depuis le formulaire
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];

        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $decalage);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodeCesarView();

        // Afficher le contenu de la page
        $view->render();

    }
}
