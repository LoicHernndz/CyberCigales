<?php
namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Cesar\DecodeCesarView;

class DechiffrementCesar extends AbstractController
{
    function getMethod(){
        $view = new DecodeCesarView();
        $view->render();
    }

    function postMethod(){
        $texte = $_POST['word-cesar'];
        $decalage = $_POST['shift-cesar'];
        $texte_chiffre_a_verifier = $_POST['word-cesar-verify'];
        if (!isset($_POST['btn-submit'])) {
            \helpers\Code\Cesar::verification($texte, $texte_chiffre_a_verifier, 'decrypt', $decalage);
        }else {
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
        $view = new DecodeCesarView();
        $view->render();
    }
}
