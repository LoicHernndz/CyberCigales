<?php
namespace Controllers\Code\Cesar;

use Controllers\AbstractController;
use Views\Code\Cesar\DecodeCesarView;

class DecryptCesar extends AbstractController
{

    // Méthode principale exécutée lorsque la route "/user/profil" est appelée en GET
    function getMethod(){

        // Créer une instance de la vue CodePermutationView
        $view = new DecodeCesarView();

        // Afficher le contenu de la page
        $view->render();
    }

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
