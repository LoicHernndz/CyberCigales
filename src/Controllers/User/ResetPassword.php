<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Views\User\ResetPasswordView;

class ResetPassword implements ControllerInterface
{
    function control(){
        $view = new ResetPasswordView();
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/reset-password" && $method === "GET";
    }
}