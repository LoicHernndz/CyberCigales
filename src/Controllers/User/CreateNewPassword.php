<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Views\User\CreateNewPasswordView;

class CreateNewPassword implements ControllerInterface
{
    function control(){
        if(empty($_GET['selector']) || empty($_GET['validator'])) {
            echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
        } else {
            $selector = $_GET['selector'];
            $validator = $_GET['validator'];

            if(ctype_xdigit($selector) && ctype_xdigit($validator)) {
                $view = new CreateNewPasswordView();
                $view->render();
            }else {
                echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
            }
        }
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/login" && $method === "GET";
    }
}