<?php
namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Cesar\CodeCesarView;

class ChiffrementCesar extends AbstractController
{
    function getMethod(){
        $view = new CodeCesarView();
        $view->render();
    }

    function postMethod(){
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'encrypt', $decalage);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new CodeCesarView();
        $view->render();
    }
}
